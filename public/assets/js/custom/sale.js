"use strict";

// Sidebar compress style
$(document).ready(function () {
    var $sidebarPlan = $(".lg-sub-plan");
    var $subPlan = $(".sub-plan");
    var isActive = $(window).width() >= 1150;

    // Toggle the “active” class on load
    $(".side-bar, .section-container").toggleClass("active", isActive);

    // Show/hide plans based on width
    if (isActive) {
        $sidebarPlan.hide();
        $subPlan.show();
    } else {
        $sidebarPlan.show();
        $subPlan.hide();
    }
});

// currency format
function currencyFormat(amount, type = "icon", decimals = 2) {
    let symbol = $("#currency_symbol").val();
    let position = $("#currency_position").val();
    let code = $("#currency_code").val();

    let formatted_amount = formattedAmount(amount, decimals);

    // Apply currency format based on the position and type
    if (type === "icon" || type === "symbol") {
        if (position === "right") {
            return formatted_amount + symbol;
        } else {
            return symbol + formatted_amount;
        }
    } else {
        if (position === "right") {
            return formatted_amount + " " + code;
        } else {
            return code + " " + formatted_amount;
        }
    }
}

// Format the amount
function formattedAmount(amount, decimals) {
    return Number.isInteger(+amount)
        ? parseInt(amount)
        : (+amount).toFixed(decimals);
}

// get number only
function getNumericValue(value) {
    return parseFloat(value.replace(/[^0-9.-]+/g, "")) || 0;
}

// Round option wise round amount
function RoundingTotal(amount) {
    let option = $("#rounding_amount_option").val();
    if (option === "round_up") {
        return Math.ceil(amount);
    } else if (option === "nearest_whole_number") {
        return Math.round(amount);
    } else if (option === "nearest_0.05") {
        return Math.round(amount * 20) / 20;
    } else if (option === "nearest_0.1") {
        return Math.round(amount * 10) / 10;
    } else if (option === "nearest_0.5") {
        return Math.round(amount * 2) / 2;
    } else {
        return amount;
    }
}

// Update the cart list and call the callback once complete
function fetchUpdatedCart(callback) {
    let url = $("#get-cart").val();
    $.ajax({
        url: url,
        type: "GET",
        success: function (response) {
            $("#cart-list").html(response);
            if (typeof callback === "function") callback();
        },
    });
}

// Update price
$(document).on("change", ".cart-price", function () {
    let $row = $(this).closest("tr");
    let rowId = $row.data("row_id");
    let updateRoute = $row.data("update_route");
    let newPrice = parseFloat($(this).val());

    if (newPrice < 0 || isNaN(newPrice)) {
        toastr.error("Price can not be negative.");
        return;
    }
    let currentQty = parseFloat($row.find(".cart-qty").val());

    updateCart(rowId, currentQty, updateRoute, newPrice);
});

// Increase quantity
$(document).on("click", ".plus-btn", function (e) {
    e.preventDefault();
    let $row = $(this).closest("tr");
    let rowId = $row.data("row_id");
    let updateRoute = $row.data("update_route");
    let $qtyInput = $row.find(".cart-qty");
    let currentQty = parseFloat($qtyInput.val());
    let newQty = currentQty + 1;
    $qtyInput.val(newQty);

    // Get the current price
    let currentPrice = parseFloat($row.find(".cart-price").val());

    if (isNaN(currentPrice) || currentPrice < 0) {
        toastr.error("Price can not be negative.");
        return;
    }
    updateCart(rowId, newQty, updateRoute, currentPrice);
});

// Decrease quantity
$(document).on("click", ".minus-btn", function (e) {
    e.preventDefault();
    let $row = $(this).closest("tr");
    let rowId = $row.data("row_id");
    let updateRoute = $row.data("update_route");
    let $qtyInput = $row.find(".cart-qty");
    let currentQty = parseFloat($qtyInput.val());

    // Ensure quantity does not go below 1
    if (currentQty > 1) {
        let newQty = currentQty - 1;
        $qtyInput.val(newQty);

        // Get the current price
        let currentPrice = parseFloat($row.find(".cart-price").val());
        if (isNaN(currentPrice) || currentPrice < 0) {
            toastr.error("Price can not be negative.");
            return;
        }

        // Call updateCart with both qty and price
        updateCart(rowId, newQty, updateRoute, currentPrice);
    }
});

// Cart quantity input field change event
$(document).on("change", ".cart-qty", function () {
    let $row = $(this).closest("tr");
    let rowId = $row.data("row_id");
    let updateRoute = $row.data("update_route");
    let newQty = parseFloat($(this).val());

    // Retrieve the cart price
    let currentPrice = parseFloat($row.find(".cart-price").val());
    if (isNaN(currentPrice) || currentPrice < 0) {
        toastr.error("Price can not be negative.");
        return;
    }

    // Ensure quantity does not go below 0
    if (newQty >= 0) {
        updateCart(rowId, newQty, updateRoute, currentPrice);
    }
});

// Remove item from the cart
$(document).on("click", ".remove-btn", function (e) {
    e.preventDefault();
    var $row = $(this).closest("tr");
    var destroyRoute = $row.data("destroy_route");

    $.ajax({
        url: destroyRoute,
        type: "DELETE",
        success: function (response) {
            if (response.success) {
                // Item was successfully removed, fade out and remove the row from DOM
                $row.fadeOut(400, function () {
                    $(this).remove();
                });
                // Recalculate and update cart totals
                fetchUpdatedCart(calTotalAmount);
            } else {
                toastr.error(response.message || "Failed to remove item");
            }
        },
        error: function () {
            toastr.error("Error removing item from cart");
        },
    });
});

// Function to update cart item with the new quantity
function updateCart(rowId, qty, updateRoute, price) {
    $.ajax({
        url: updateRoute,
        type: "PUT",
        data: {
            rowId: rowId,
            qty: qty,
            price: price,
        },
        success: function (response) {
            if (response.success) {
                fetchUpdatedCart(calTotalAmount); // Refresh the cart and recalculate totals
            } else {
                toastr.error(response.message || "Failed to update cart");
            }
        },
    });
}

// Clear the cart and then refresh the UI with updated values
function clearCart(cartType) {
    let route = $("#clear-cart").val();
    $.ajax({
        type: "POST",
        url: route,
        data: {
            type: cartType,
        },
        dataType: "json",
        success: function () {
            fetchUpdatedCart(calTotalAmount); // Call calTotalAmount after cart fetch completes
        },
        error: function () {
            console.error("There was an issue clearing the cart.");
        },
    });
}

/** Handle customer selection change **/
$(".customer-select").on("change", function () {
    let customerType = $(this).find(":selected").data("type");

    let cartRows = []; // Collect cart stock_id + batch_no if cart is not empty
    $("#cart-list tr").each(function () {
        let $row = $(this);
        let stockId = $row.data("stock_id");
        let batchNo = $row.data("batch_no") || null;

        if (stockId) {
            cartRows.push({ stock_id: stockId, batch_no: batchNo });
        }
    });

    let route = $("#get_stock_prices").val();

    $.ajax({
        url: route,
        type: "POST",
        data: {
            type: customerType,
            stocks: cartRows.length ? cartRows : null, // Optional for cart list
        },
        success: function (response) {
            // Update product list
            $(".single-product").each(function () {
                let productId = $(this).data("product_id");
                if (response.products[productId]) {
                    $(this).find(".product_price").text(response.products[productId]);
                }
            });

            // Update cart cart list if cart not empty
            if (cartRows.length) {
                $("#cart-list tr").each(function () {
                    let $row = $(this);
                    let stockId = $row.data("stock_id");
                    let batchNo = $row.data("batch_no") || "default";

                    if (stockId && response.stocks[stockId]) {
                        if (batchNo && response.stocks[stockId][batchNo]) {
                            $row.find(".cart-price").val(response.stocks[stockId][batchNo]).trigger('change');
                        } else if (response.stocks[stockId]['default']) {
                            $row.find(".cart-price").val(response.stocks[stockId]['default']).trigger('change');
                        }
                    }
                });
                // Recalculate totals after cart update
                calTotalAmount();
            }
        }
    });
});

// Trigger calculation whenever Discount, or Receive Amount fields change
$("#discount_amount, #receive_amount, #shipping_charge").on(
    "input",
    function () {
        calTotalAmount();
    }
);

// tax calculation
$(".tax_select").on("change", function () {
    let taxRate = parseFloat($(this).find(":selected").data("rate")) || 0;
    let subtotal = getNumericValue($("#sub_total").text()) || 0;

    let taxAmount = (subtotal * taxRate) / 100;

    $("#tax_amount").val(taxAmount.toFixed(2));
    calTotalAmount();
});

// discount calculation
$(".discount_type").on("change", function () {
    calTotalAmount();
});

// Function to calculate the total amount
function calTotalAmount() {

    let subtotal = 0;

    // Calculate subtotal from cart list using qty * price
    $("#cart-list tr").each(function () {
        let qty = getNumericValue($(this).find(".cart-qty").val()) || 0;
        let price = getNumericValue($(this).find(".cart-price").val()) || 0;
        let row_subtotal = qty * price;
        subtotal += row_subtotal;
    });

    $("#sub_total").text(currencyFormat(subtotal));

    // tax
    let tax_rate =
        parseFloat($(".tax_select option:selected").data("rate")) || 0;
    let tax_amount = (subtotal * tax_rate) / 100;
    $("#tax_amount").val(tax_amount.toFixed(2));

    // Subtotal with Tax
    let subtotal_with_tax = subtotal + tax_amount;

    // Discount
    let discount_amount = getNumericValue($("#discount_amount").val()) || 0;
    let discount_type = $(".discount_type").val(); // Get the selected discount type

    if (discount_type == "percent") {
        discount_amount = (subtotal_with_tax * discount_amount) / 100;

        // Ensure percentage discount does not exceed 100%
        if (discount_amount > subtotal_with_tax) {
            toastr.error("Discount cannot be more than 100% of the subtotal!");
            discount_amount = subtotal_with_tax; // Cap discount at subtotal
            $("#discount_amount").val(100); // Reset input field to max 100%
        }
    } else {
        if (discount_amount > subtotal_with_tax) {
            toastr.error("Discount cannot be more than the subtotal!");
            discount_amount = subtotal_with_tax;
            $("#discount_amount").val(discount_amount);
        }
    }

    //Shipping Charge
    let shipping_charge = getNumericValue($("#shipping_charge").val()) || 0;

    // Total Amount
    let total_amount =
        subtotal_with_tax + shipping_charge - discount_amount;
    $("#total_amount").text(currencyFormat(total_amount));

    let rounding_total = RoundingTotal(total_amount);

    // Rounding off
    let rounding_amount = Math.abs(rounding_total - total_amount);
    $("#rounding_amount").text(currencyFormat(rounding_amount));

    // Payable Amount
    let payable_amount = rounding_total;
    $("#payable_amount").text(currencyFormat(payable_amount));

    // Receive Amount
    let receive_amount = getNumericValue($("#receive_amount").val()) || 0;
    if (receive_amount < 0) {
        toastr.error("Receive amount cannot be less than 0!");
        receive_amount = 0;
        $("#receive_amount").val(receive_amount);
    }

    // Change Amount
    let change_amount =
        receive_amount > payable_amount ? receive_amount - payable_amount : 0;
    $("#change_amount").val(formattedAmount(change_amount, 2));

    // Due Amount
    let due_amount =
        payable_amount > receive_amount ? payable_amount - receive_amount : 0;
    $("#due_amount").val(formattedAmount(due_amount, 2));
}

calTotalAmount();

// Cancel btn action
$(".cancel-sale-btn").on("click", function (e) {
    e.preventDefault();
    clearCart("sale");
    $("#receive_amount").val("");
    $(".null_by_reset").val("");
    $(".customer-select").val("");
});

// product filter by category & manufacturer
$(document).on("change", "#category_id, #manufacturer_id", function () {
    fetchProducts();
});

/** Add to cart start **/

// Debounce function to limit the frequency of API calls
function debounce(func, delay) {
    let timer;
    return function (...args) {
        const context = this;
        clearTimeout(timer);
        timer = setTimeout(() => func.apply(context, args), delay);
    };
}

// Scanner detection variables
let isScannerInput = false;
let scannerInputTimeout;
const SCANNER_LOCK_TIME = 300; // Time to wait before allowing another scan

// Handle barcode scanner input (Enter key detection)
$(".product-filter").on("keydown", ".search-input", function (e) {
    if (e.key === "Enter") {
        if (isScannerInput) {
            e.preventDefault();
            return; // Skip duplicate scanner calls
        }

        e.preventDefault(); // Prevent form submission
        handleScannerInput(this);
    }
});

$(".product-filter").on("submit", function (e) {
    e.preventDefault();
});

// Handle the input event with debouncing
$(".product-filter").on(
    "input",
    ".search-input",
    debounce(function () {
        if (isScannerInput) {
            return; // Skip input events triggered by scanner
        }

        handleUserInput();
    }, 400)
);

// Function to handle scanner input
function handleScannerInput(inputElement) {
    isScannerInput = true; // Lock scanner input handling
    clearTimeout(scannerInputTimeout); // Reset scanner lock timer

    const form = $(inputElement).closest("form")[0];

    $.ajax({
        type: "POST",
        url: $(form).attr("action"),
        data: new FormData(form),
        dataType: "json",
        contentType: false,
        cache: false,
        processData: false,
        success: function (res) {
            if (res.total_products && res.product_id) {
                autoAddItemToCart(res.product_id);
            }
            // $("#products-list").html(res.data); // Update the table with new data
            // change price according customer-type
            customerWisePrice();
        },
        complete: function () {
            resetScannerLock();
        },
    });
}

// Function to handle user input
function handleUserInput() {
    fetchProducts();
}

// Reset scanner lock after processing
function resetScannerLock() {
    scannerInputTimeout = setTimeout(() => {
        isScannerInput = false;
    }, SCANNER_LOCK_TIME);
}

// Fetch products function
function fetchProducts() {
    const form = $(".product-filter-form")[0];
    $.ajax({
        type: "POST",
        url: $(form).attr("action"),
        data: new FormData(form),
        dataType: "json",
        contentType: false,
        cache: false,
        processData: false,
        success: function (res) {
            $("#products-list").html(res.data); // Update the table with new data
            if (
                res.total_products &&
                res.product_id &&
                res.total_products_count > 1
            ) {
                autoAddItemToCart(res.product_id);
            }
            // change price according customer-type
            customerWisePrice();
        },
    });
}

// Customer Wise Product Price Change
function customerWisePrice() {
    let customer_type =
        $(".customer-select option:selected").data("type") || "Retailer";
    if (customer_type && customer_type !== "Retailer") {
        let url = $("#get_product").val();

        $.ajax({
            url: url,
            type: "GET",
            data: { type: customer_type },
            success: function (data) {
                $(".single-product").each(function () {
                    let productId = $(this).data("product_id");
                    if (data[productId]) {
                        $(this).find(".product_price").text(data[productId]);
                    }
                });
            },
        });
    }
}
// ------------------------
// Utility Functions
// ------------------------
function getCustomerType() {
    return $(".customer-select option:selected").data("type") || "Retailer";
}

function getAvailableStocks(stocks) {
    return Array.isArray(stocks)
        ? stocks.filter((stock) => parseFloat(stock.productStock) >= 1)
        : [];
}

function getAdjustedPrice(batch, customerType) {
    if (customerType === "Dealer" && batch.dealer_price) {
        return batch.dealer_price;
    } else if (customerType === "Wholesaler" && batch.wholesale_price) {
        return batch.wholesale_price;
    }
    return batch.sales_price;
}

function prepareSingleBatchItem(item, batch, customerType) {
    item.data("product_stock_id", batch.id);
    item.data("product_expire_date", batch.expire_date);
    item.data("default_price", getAdjustedPrice(batch, customerType));
    return item;
}

function showBatchSelectionModal(element, availableStocks, customerType) {
    let html = availableStocks
        .map((batch, index) => {
            const adjustedPrice = getAdjustedPrice(batch, customerType);

            return `
            <tr class="select-batch"
                data-product_id="${element.data("product_id")}"
                data-product_stock_id="${batch.id}"
                data-product_expire_date="${batch.expire_date ?? ""}"
                data-product_name="${element.data("product_name")}"
                data-product_code="${element.data("product_code")}"
                data-default_price="${adjustedPrice}"
                data-product_unit_id="${element.data("product_unit_id")}"
                data-product_unit_name="${element.data("product_unit_name")}"
                data-purchase_exclusive_price="${batch.purchase_without_tax ?? 0}"
                data-purchase_inclusive_price="${batch.purchase_with_tax ?? 0}"
                data-product_image="${element.data("product_image")}"
                data-route="${element.data("route")}">
                    <td>${index + 1}</td>
                    <td class="text-start">${element.data("product_name")}</td>
                    <td>${batch.batch_no ?? ""}</td>
                    <td>${batch.productStock ?? ""}</td>
                    <td class="product_price">${currencyFormat(
                adjustedPrice
            )}</td>
            </tr>`;
        })
        .join("");

    $(".stock-table").html(html);
    $("#stock-list-modal").modal("show");
}

// ------------------------
// Core Add-to-Cart Logic
// ------------------------

function handleAddToCart(element) {
    const batchCount = parseInt(element.data("batch_count")) || 0;
    const stocks = Array.isArray(element.data("stocks"))
        ? element.data("stocks")
        : [];
    const customerType = getCustomerType();
    const availableStocks = getAvailableStocks(stocks);

    if (batchCount > 1 && availableStocks.length > 0) {
        showBatchSelectionModal(element, availableStocks, customerType);
        return;
    }

    // Only one batch or no modal needed
    const singleBatch = stocks[0] ?? {};
    // Force adjust price according to customer type
    const adjustedPrice = getAdjustedPrice(singleBatch, customerType);

    element.data("default_price", adjustedPrice); // override old price
    element.find(".product_price").text(currencyFormat(adjustedPrice));

    const item = prepareSingleBatchItem(element, singleBatch, customerType);
    addItemToCart(item);
}


// ------------------------
// Auto Add From Scanner
// ------------------------
function autoAddItemToCart(id) {
    const element = $("#products-list").find(".single-product." + id);
    handleAddToCart(element);
}


// ------------------------
// Click Event Binding
// ------------------------
$(document).on("click", "#single-product", function () {
    handleAddToCart($(this));
});

// Handle click on a batch inside the modal
$(document).on("click", ".select-batch", function () {
    addItemToCart($(this));
    $("#stock-list-modal").modal("hide");
});

// search filter in modal
$(document).on("keyup", ".stock-search", function () {
    let value = $(this).val().toLowerCase();
    $(".stock-table tr").filter(function () {
        $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
    });
});

function addItemToCart(element) {
    let url = element.data("route");
    let product_id = element.data("product_id");
    let product_unit_name = element.data("product_unit_name");
    let product_name = element.find(".product_name").text() || element.data("product_name");
    let extractedPrice = getNumericValue(element.find(".product_price").text());
    let product_price =
        !isNaN(extractedPrice) && extractedPrice > 0
            ? extractedPrice
            : parseFloat(element.data("default_price")) || 0;
    let purchase_exclusive_price = element.data("purchase_exclusive_price");
    let purchase_inclusive_price = element.data("purchase_inclusive_price");
    let product_stock_id = element.data("product_stock_id");
    let expire_date = element.data("expire_date");
    let product_image = element.data("product_image");

    $.ajax({
        url: url,
        type: "POST",
        data: {
            type: "sale",
            id: product_id,
            name: product_name,
            price: product_price,
            product_unit_name: product_unit_name,
            quantity: 1,
            stock_id: product_stock_id,
            purchase_exclusive_price: purchase_exclusive_price,
            purchase_inclusive_price: purchase_inclusive_price,
            expire_date: expire_date,
            product_image: product_image,
        },
        success: function (response) {
            if (response.success) {
                fetchUpdatedCart(calTotalAmount); // Update totals after cart fetch completes
                $("#sale_product_search").val("");
            } else {
                toastr.error(response.message);
            }
        },
        error: function (xhr) {
            console.error("Error:", xhr.responseText);
        },
    });
}

/** Add to cart End **/
