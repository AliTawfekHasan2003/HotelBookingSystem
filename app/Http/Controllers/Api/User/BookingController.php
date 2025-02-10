<?php

namespace App\Http\Controllers\Api\User;

use App\Events\InvoicePaid;
use App\Http\Controllers\Controller;
use App\Http\Requests\BookingRequest;
use App\Http\Requests\ConfirmPaymentRequest;
use App\Models\Booking;
use App\Models\Invoice;
use App\Models\Room;
use App\Models\Service;
use App\Traits\CalculateCostTrait;
use App\Traits\ResponseTrait;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Stripe\StripeClient;
use Throwable;

class BookingController extends Controller
{
    use ResponseTrait, CalculateCostTrait;

    /**   @OA\Post(
     *     path="/api/user/bookings/calculate_cost",
     *     summary="Calculate Cost Of Booking",
     *     tags={"Bookings"},
     *     operationId="calculateCostBooking",
     *     security={{"bearerAuth": {}}},
     *     description="Calculate cost of booking",
     *     @OA\Parameter(
     *         name="Accept-language",
     *         in="header",
     *         description="Set the language for the response (e.g., 'en' for English, 'ar' for Arabic)",
     *         required=true,
     *         @OA\Schema(type="string", enum={"en", "ar"})
     *     ),
     *     @OA\RequestBody(
     *          required=true,
     *          description="Booking details",
     *         @OA\JsonContent(
     *               required={"room_id", "start_date", "end_date"},
     *               @OA\Property(property="room_id", type="integer", example=23, description="Required. Must exist in the rooms table. Must room available during the specified period."),
     *               @OA\Property(property="services", type="array", description="Nullable. Must unique",
     *               @OA\Items(
     *                     type="integer",
     *                     example=1,
     *                     description="Must exist in the services table. Must assign to room type. Must service own units during the specified period(if it’s limited)."
     *                 )       
     *               ),
     *               @OA\Property(property="start_date", type="string", format="date", example="2025-12-01", description="Required. Must be a valid date in Y-m-d format and must be after today's date."),
     *               @OA\Property(property="end_date", type="string", format="date", example="2025-12-10", description="Required. Must be a valid date in Y-m-d format and must be after the start_date.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Total cost calculate successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="msg", type="string", example="The total cost for the booking calculate successfully."),
     *             @OA\Property(property="code", type="integer", example=200),
     *             @OA\Property(
     *                 property="bookingCost",
     *                 type="object",
     *                 @OA\Property(property="total_cost", type="float", example=709.01),
     *                 @OA\Property(property="room_cost", type="float", example=709.01),
     *                 @OA\Property(property="services_cost", type="float", example=0.00),
     *                 @OA\Property(property="count_month", type="integer", example=3),
     *                 @OA\Property(property="count_day", type="integer", example=12)
     *             ) 
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(
     *                 property="msg",
     *                 type="object",
     *                 @OA\Property(
     *                   property="room_id",
     *                   type="array",
     *                   @OA\Items(type="string", example="The provided room type does not exist in our records.")
     *                 ),
     *                 @OA\Property(
     *                   property="services",
     *                   type="array",
     *                   @OA\Items(type="string", example="This field must be an array.")
     *                 ),
     *                 @OA\Property(
     *                   property="services.1",
     *                   type="array",
     *                   @OA\Items(type="string", example="The provided service does not exist in our records.")
     *                 ),
     *                 @OA\Property(
     *                   property="service_id => 1",
     *                   type="array",
     *                   @OA\Items(type="string", example="This service not assign to room type.")
     *                 ),
     *                 @OA\Property(
     *                   property="start_date",
     *                   type="array",
     *                   @OA\Items(type="string", example="The start date must be after today.")
     *                 ),
     *                 @OA\Property(
     *                   property="end_date",
     *                   type="array",
     *                   @OA\Items(type="string", example="The end date must be after start date.")
     *                 )
     *             ),
     *             @OA\Property(property="code", type="integer", example=422)
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Unable access this section",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="msg", type="string", example="You don’t have permission to access this section."),
     *             @OA\Property(property="code", type="integer", example=403)
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized: Invalid token or expired.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="msg", type="string", example="Unauthorized: Your session is invalid, Please log in again."),
     *             @OA\Property(property="code", type="integer", example=401)
     *         )
     *     )
     * )
     */

    public function  calculateCost(BookingRequest $request)
    {
        $room = Room::with('roomType')->find($request->room_id);
        $servicesId = $request->services;
        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);

        $roomCost = $this->handelPrice($startDate, $endDate, $room->roomType->monthly_price, $room->roomType->daily_price);
        $servicesCost = 0.00;

        if ($servicesId) {
            $services =  Service::whereIN('id', $servicesId)->get();

            $servicesCost = $this->calculateServicesCost($services, $startDate, $endDate);
        }

        $totalCost = $servicesCost + $roomCost;
        $dates = $this->handelDate($startDate, $endDate);

        $bookingCost = [
            'total_cost' => number_format($totalCost, 2),
            'room_cost' => number_format($roomCost, 2),
            'services_cost' => number_format($servicesCost, 2),
            'count_month' => $dates['countMonth'],
            'count_day' => $dates['countDay'],
        ];

        return $this->returnData(true, __('success.booking.total_cost'), 'bookingCost', $bookingCost);
    }

    /**   @OA\Post(
     *     path="/api/user/bookings/payment_intent",
     *     summary="Payment Intent Booking",
     *     tags={"Bookings"},
     *     operationId="paymentIntentBooking",
     *     security={{"bearerAuth": {}}},
     *     description="Payment intent booking",
     *     @OA\Parameter(
     *         name="Accept-language",
     *         in="header",
     *         description="Set the language for the response (e.g., 'en' for English, 'ar' for Arabic)",
     *         required=true,
     *         @OA\Schema(type="string", enum={"en", "ar"})
     *     ),
     *     @OA\RequestBody(
     *          required=true,
     *          description="Booking details",
     *         @OA\JsonContent(
     *               required={"room_id", "start_date", "end_date", "payment_method_id"},
     *               @OA\Property(property="room_id", type="integer", example=23, description="Required. Must exist in the rooms table. Must room available during the specified period."),
     *               @OA\Property(property="services", type="array", description="Nullable. Must unique",
     *               @OA\Items(
     *                     type="integer",
     *                     example=1,
     *                     description="Must exist in the services table. Must assign to room type. Must service own units during the specified period(if it’s limited)."
     *                 )       
     *               ),
     *               @OA\Property(property="start_date", type="string", format="date", example="2025-12-01", description="Required. Must be a valid date in Y-m-d format and must be after today's date."),
     *               @OA\Property(property="end_date", type="string", format="date", example="2025-12-10", description="Required. Must be a valid date in Y-m-d format and must be after the start_date."),
     *               @OA\Property(property="payment_method_id", type="string", example="pm_jonnjbbbb_polkkm8_op9", description="Required. Must be start with (pm_) and contain only letters, numbers, and underscores(_). Length: min=10.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Payment initiated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="msg", type="string", example="Payment initiated successfully."),
     *             @OA\Property(property="code", type="integer", example=201),
     *             @OA\Property(
     *                 property="paymentInformation",
     *                 type="object",
     *                 @OA\Property(property="payment_id", type="string", example="pi_ghuk_kjhjj_kkjjnn_io"),
     *                 @OA\Property(property="client_secret", type="string", example="lkkij09iijnij"),
     *                 @OA\Property(property="total_cost", type="float", example=709.01),
     *             ) 
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(
     *                 property="msg",
     *                 type="object",
     *                 @OA\Property(
     *                   property="room_id",
     *                   type="array",
     *                   @OA\Items(type="string", example="The provided room type does not exist in our records.")
     *                 ),
     *                 @OA\Property(
     *                   property="services",
     *                   type="array",
     *                   @OA\Items(type="string", example="This field must be an array.")
     *                 ),
     *                 @OA\Property(
     *                   property="services.1",
     *                   type="array",
     *                   @OA\Items(type="string", example="The provided service does not exist in our records.")
     *                 ),
     *                 @OA\Property(
     *                   property="service_id => 1",
     *                   type="array",
     *                   @OA\Items(type="string", example="This service not assign to room type.")
     *                 ),
     *                 @OA\Property(
     *                   property="start_date",
     *                   type="array",
     *                   @OA\Items(type="string", example="The start date must be after today.")
     *                 ),
     *                 @OA\Property(
     *                   property="end_date",
     *                   type="array",
     *                   @OA\Items(type="string", example="The end date must be after start date.")
     *                 ),
     *                 @OA\Property(
     *                   property="payment_method_id",
     *                   type="array",
     *                   @OA\Items(type="string", example="The payment method ID must start with (pm_) and contain only letters, numbers, and underscores(_).")
     *                 )
     *             ),
     *             @OA\Property(property="code", type="integer", example=422)
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Unable access this section",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="msg", type="string", example="You don’t have permission to access this section."),
     *             @OA\Property(property="code", type="integer", example=403)
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized: Invalid token or expired.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="msg", type="string", example="Unauthorized: Your session is invalid, Please log in again."),
     *             @OA\Property(property="code", type="integer", example=401)
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="An unexpected error occurred",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="msg", type="string", example="Unexpected error occurred during payment, Please try again later."),
     *             @OA\Property(property="code", type="integer", example=500)
     *         )
     *     )
     * )
     */

    public function paymentIntent(BookingRequest $request)
    {
        $room = Room::with('roomType')->find($request->room_id);
        $servicesId = $request->services;
        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);

        $roomCost = $this->handelPrice($startDate, $endDate, $room->roomType->monthly_price, $room->roomType->daily_price);
        $servicesCost = 0.00;

        if ($servicesId) {
            $services =  Service::whereIN('id', $servicesId)->get();

            $servicesCost = $this->calculateServicesCost($services, $startDate, $endDate);
        }

        $totalCost = $servicesCost + $roomCost;
        $dates = $this->handelDate($startDate, $endDate);

        DB::beginTransaction();

        try {
            $stripe = new StripeClient(config('services.stripe.secret'));

            $amount = intval($totalCost * 100);

            $paymentIntent = $stripe->paymentIntents->create([
                'amount' => $amount,
                'currency' => 'usd',
                'payment_method' => $request->payment_method_id,
                'confirmation_method' => 'automatic',
                'confirm' => true,
                'description' => 'Room Booking payment',
            ]);

            $invoice  =  Invoice::addInvoice($startDate, $endDate, $dates['countMonth'], $dates['countDay'], $totalCost, $paymentIntent->id);

            Booking::addBooking($invoice->id, $room->roomType->monthly_price, $room->roomType->daily_price, $roomCost, ['type' => 'room', 'id' => $room->id]);

            if ($services->isNotEmpty()) {
                foreach ($services as $service) {
                    Booking::addBooking(
                        $invoice->id,
                        $service->monthly_price,
                        $service->daily_price,
                        $this->handelPrice($startDate, $endDate, $service->monthly_price, $service->daily_price),
                        ['type' => 'service', 'id' => $service->id],
                    );
                }
            }

            $paymentInformation = [
                'payment_id' => $paymentIntent->id,
                'client_secret' => $paymentIntent->client_secret,
                'total_cost' => round($totalCost, 2),
            ];
            DB::commit();

            return $this->returnData(true, __('success.booking.payment_intent'), 'paymentInformation', $paymentInformation, 201);
        } catch (Throwable $e) {
            DB::rollBack();

            Log::error('Error occurred during payment: ' . $e->getMessage());

            return $this->returnError(__('errors.unexpected_error'), 500);
        }
    }

    /**   @OA\Post(
     *     path="/api/user/bookings/confirm_payment",
     *     summary="Confirm Payment Booking",
     *     tags={"Bookings"},
     *     operationId="ConfirmpaymentBooking",
     *     security={{"bearerAuth": {}}},
     *     description="Confirm payment booking",
     *     @OA\Parameter(
     *         name="Accept-language",
     *         in="header",
     *         description="Set the language for the response (e.g., 'en' for English, 'ar' for Arabic)",
     *         required=true,
     *         @OA\Schema(type="string", enum={"en", "ar"})
     *     ),
     *     @OA\RequestBody(
     *          required=true,
     *          description="Confirm booking details",
     *         @OA\JsonContent(
     *               required={"payment_id", "payment_status"},
     *               @OA\Property(property="payment_id", type="string", example="pi_jonnjbbbb_polkkm8_op9", description="Required. Must be start with (pi_) and contain only letters, numbers, and underscores(_). Length: min=10."),
     *               @OA\Property(property="payment_status", type="string", enum={"succeeded", "failed"}, description="Required. Must be one of the following: [succeeded, failed].")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Payment confirmed successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="msg", type="string", example="Payment confirmed successfully"),
     *             @OA\Property(property="code", type="integer", example=200),
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(
     *                 property="msg",
     *                 type="object",
     *                 @OA\Property(
     *                   property="payment_id",
     *                   type="array",
     *                   @OA\Items(type="string", example="The payment ID must start with (pi_) and contain only letters, numbers, and underscores(_).")
     *                 ),
     *                 @OA\Property(
     *                   property="payment_status",
     *                   type="array",
     *                   @OA\Items(type="string", example="The status must be one of the following: [succeeded, failed].")
     *                 )
     *             ),
     *             @OA\Property(property="code", type="integer", example=422)
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Unable access this section",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="msg", type="string", example="You don’t have permission to access this section."),
     *             @OA\Property(property="code", type="integer", example=403)
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Payment failed.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="msg", type="string", example="Payment failed, Booking has been cancelled."),
     *             @OA\Property(property="code", type="integer", example=400)
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Invoice not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="msg", type="string", example="Invoice not found."),
     *             @OA\Property(property="code", type="integer", example=404)
     *         )
     *     ),
     *     @OA\Response(
     *         response=409,
     *         description="The invoice status not pending.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="msg", type="string", example="The invoice status not pending."),
     *             @OA\Property(property="code", type="integer", example=409)
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized: Invalid token or expired.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="msg", type="string", example="Unauthorized: Your session is invalid, Please log in again."),
     *             @OA\Property(property="code", type="integer", example=401)
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="An unexpected error occurred",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="msg", type="string", example="Unexpected error occurred during confirm payment, Please try again later."),
     *             @OA\Property(property="code", type="integer", example=500)
     *         )
     *     )
     * )
     */

    public function confirmPayment(ConfirmPaymentRequest $request)
    {
        DB::beginTransaction();
        try {
            $invoice = Invoice::where('payment_id', $request->payment_id)->first();

            if (!$invoice) {
                return $this->returnError(__('errors.invoice.not_found'), 404);
            }

            if ($invoice->status !== 'pending') {
                return $this->returnError(__('errors.invoice.not_pending'), 409);
            }

            $status = $request->payment_status === 'succeeded' ? 'paid' : 'cancelled';

            $invoice->update([
                'status' => $status,
            ]);
            DB::commit();

            if ($status === 'paid') {
                event(new InvoicePaid($invoice));

                return $this->returnSuccess(__('success.booking.payment_confirm'));
            } else {
                return $this->returnError(__('errors.booking.payment_failed'));
            }
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error('Error occurred during confirm payment: ' . $e->getMessage());

            return $this->returnError(__('errors.unexpected_error'), 500);
        }
    }
}
