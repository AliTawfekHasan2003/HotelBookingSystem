<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\BookingResource;
use App\Http\Resources\InvoiceResource;
use App\Models\Invoice;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    use ResponseTrait;

    /**   @OA\Get(
     *     path="/api/user/invoices",
     *     summary="Get List Of Invoices",
     *     tags={"Invoices"},
     *     operationId="getUserInvoices",
     *     security={{"bearerAuth": {}}},
     *     description="Get invoices for user with 10 pagination", 
     *    @OA\Parameter(
     *         name="Accept-language",
     *         in="header",
     *         description="Set the language for the response (e.g., 'en' for English, 'ar' for Arabic)",
     *         required=true,
     *         @OA\Schema(type="string", enum={"en", "ar"})
     *    ),
     *    @OA\Parameter(
     *         name="status",
     *         in="query",
     *         description="Filter by status.",
     *         required=false,
     *         @OA\Schema(type="string", enum={"paid", "pending", "cancelled"})
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Get invoices successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="msg", type="string", example="Your invoices fetched successfully."),
     *             @OA\Property(property="code", type="integer", example=200),
     *             @OA\Property(
     *                 property="invoices",
     *                 type="array",
     *                 description="List of invoices",
     *                 @OA\Items(
     *                   type="object",
     *                   @OA\Property(property="id", type="integer", example=1),
     *                   @OA\Property(property="user_id", type="integer", example=3),
     *                   @OA\Property(property="start_date", type="string", format="date-time", example="2025-01-12T00:00:00.000000Z"),
     *                   @OA\Property(property="end_date", type="string", format="date-time", example="2025-02-22T00:00:00.000000Z"),
     *                   @OA\Property(property="count_month", type="integer", example=1),
     *                   @OA\Property(property="count_day", type="integer", example=3),
     *                   @OA\Property(property="total_cost", type="float", example=120.67),
     *                   @OA\Property(property="status", type="string", enum={"paid", "pending", "cancelled"}, example="paid"),
     *                   @OA\Property(property="created_at", type="string", format="date-time", example="2024-10-20T12:26:39.000000Z"),
     *                   @OA\Property(property="updated_at", type="string", format="date-time", example="2024-11-17T12:17:20.000000Z")
     *                )
     *             ), 
     *             @OA\Property(
     *                 property="pagination",
     *                 type="object",
     *                 @OA\Property(
     *                     property="meta",
     *                     type="object",
     *                     @OA\Property(property="currentPage", type="integer", example=1),
     *                     @OA\Property(property="lastPage", type="integer", example=1),
     *                     @OA\Property(property="perPage", type="integer", example=10),
     *                     @OA\Property(property="total", type="integer", example=5)
     *                  ),
     *                  @OA\Property(
     *                     property="links",
     *                     type="object",
     *                     @OA\Property(property="currentPageUrl", type="string", example="http://127.0.0.1:8000/api/notifications?page=1"),
     *                     @OA\Property(property="previousPageUrl", type="string", example=null),
     *                     @OA\Property(property="nextPageUrl", type="string", example=null),
     *                     @OA\Property(property="lastPageUrl", type="string", example="http://127.0.0.1:8000/api/notifications?page=1")
     *                  )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No invoices found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="msg", type="string", example="You dont have any invoice."),
     *             @OA\Property(property="code", type="integer", example=404)
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

    public function index(Request $request)
    {
        $result = Invoice::filterInvoices($request, auth()->id());

        $query = $result['query'];
        $ifCriteria = $result['ifCriteria'];

        $invoices = $query->paginate(10);

        if ($invoices->isEmpty()) {
            if ($ifCriteria)
                return $this->returnError(__('errors.invoice.not_found_index_with_criteria'), 404);
            else
                return $this->returnError(__('errors.invoice.not_found_index_user'), 404);
        }

        return  $this->returnPaginationData(true, __('success.invoice.index_user'), 'invoices', InvoiceResource::collection($invoices));
    }

    /**  @OA\Get(
     *     path="/api/user/invoices/{id}",
     *     summary="Show Invoice",
     *     tags={"Invoices"},
     *     operationId="showUserInvoice",
     *     security={{"bearerAuth": {}}},
     *     description="Get user invoice", 
     *    @OA\Parameter(
     *         name="Accept-language",
     *         in="header",
     *         description="Set the language for the response (e.g., 'en' for English, 'ar' for Arabic)",
     *         required=true,
     *         @OA\Schema(type="string", enum={"en", "ar"})
     *    ),
     *    @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Invoice ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Get invoice successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="msg", type="string", example="Your invoice fetched successfully."),
     *             @OA\Property(property="code", type="integer", example=200),
     *             @OA\Property(
     *                 property="invoice",
     *                 type="array",
     *                 @OA\Items(
     *                   type="object",
     *                   @OA\Property(property="id", type="integer", example=1),
     *                   @OA\Property(property="user_id", type="integer", example=3),
     *                   @OA\Property(property="start_date", type="string", format="date-time", example="2025-01-12T00:00:00.000000Z"),
     *                   @OA\Property(property="end_date", type="string", format="date-time", example="2025-02-22T00:00:00.000000Z"),
     *                   @OA\Property(property="count_month", type="integer", example=1),
     *                   @OA\Property(property="count_day", type="integer", example=3),
     *                   @OA\Property(property="total_cost", type="float", example=120.67),
     *                   @OA\Property(property="status", type="string", enum={"paid", "pending", "cancelled"}, example="paid"),
     *                   @OA\Property(property="created_at", type="string", format="date-time", example="2024-10-20T12:26:39.000000Z"),
     *                   @OA\Property(property="updated_at", type="string", format="date-time", example="2024-11-17T12:17:20.000000Z")
     *                )
     *             )
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

    public function show($id)
    {
        $invoice = Invoice::byUser(auth()->id())->find($id);

        if (!$invoice) {
            return $this->returnError(__('errors.invoice.not_found'), 404);
        }

        return $this->returnData(true, __('success.invoice.show_user'), 'invoice', new InvoiceResource($invoice));
    }

    /**  @OA\Get(
     *     path="/api/user/invoices/{id}/bookings",
     *     summary="Get List Of Bookings For Invoice",
     *     tags={"Invoices"},
     *     operationId="getUserBookingsInvoice",
     *     security={{"bearerAuth": {}}},
     *     description="Get all bookings for user invoice with 10 pagination", 
     *    @OA\Parameter(
     *         name="Accept-language",
     *         in="header",
     *         description="Set the language for the response (e.g., 'en' for English, 'ar' for Arabic)",
     *         required=true,
     *         @OA\Schema(type="string", enum={"en", "ar"})
     *    ),
     *    @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Invoice ID",
     *         @OA\Schema(type="integer")
     *     ),
     *    @OA\Response(
     *         response=200,
     *         description="Get bookings for this invoice successfully.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="msg", type="string", example="All bookings for your invoice fetched successfully."),
     *             @OA\Property(property="code", type="integer", example=200),
     *             @OA\Property(
     *                 property="bookings",
     *                 type="array",
     *                 description="List of bookings",
     *                 @OA\Items(
     *                    type="object",
     *                    @OA\Property(property="id", type="integer", example=1),
     *                    @OA\Property(property="invoice_id", type="integer", example=1),
     *                    @OA\Property(property="bookingable_type", type="string", example="service"),
     *                    @OA\Property(property="bookingable_id", type="integer", example=5),
     *                    @OA\Property(property="original_monthly_price", type="float", example=234.55),
     *                    @OA\Property(property="original_daily_price", type="float", example=2345.89),
     *                    @OA\Property(property="booking_cost", type="float", example=666676.78),
     *                 )
     *             ), 
     *             @OA\Property(
     *                 property="pagination",
     *                 type="object",
     *                 @OA\Property(
     *                     property="meta",
     *                     type="object",
     *                     @OA\Property(property="currentPage", type="integer", example=1),
     *                     @OA\Property(property="lastPage", type="integer", example=1),
     *                     @OA\Property(property="perPage", type="integer", example=10),
     *                     @OA\Property(property="total", type="integer", example=5)
     *                  ),
     *                  @OA\Property(
     *                     property="links",
     *                     type="object",
     *                     @OA\Property(property="currentPageUrl", type="string", example="http://127.0.0.1:8000/api/notifications?page=1"),
     *                     @OA\Property(property="previousPageUrl", type="string", example=null),
     *                     @OA\Property(property="nextPageUrl", type="string", example=null),
     *                     @OA\Property(property="lastPageUrl", type="string", example="http://127.0.0.1:8000/api/notifications?page=1")
     *                  )
     *             )
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

    public function bookings($id)
    {
        $invoice = Invoice::byUser(auth()->id())->find($id);

        if (!$invoice) {
            return $this->returnError(__('errors.invoice.not_found'), 404);
        }

        $bookings = $invoice->bookings()->paginate(10);

        return $this->returnPaginationData(true, __('success.invoice.bookings_user'), 'bookings', BookingResource::collection($bookings));
    }
}
