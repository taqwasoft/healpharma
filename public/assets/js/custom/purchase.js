"use strict";

// Sidebar compress style
$(".side-bar, .section-container").toggleClass(
    "active",
    window.innerWidth >= 1150
);

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

// Update the cart list and call the callback once complete
function fetchUpdatedCart(callback) {
    let url = $("#purchase-cart").val();
    $.ajax({
        url: url,
        type: "GET",
        success: function (response) {
            $("#purchase_cart_list").html(response);
            if (typeof callback === "function") callback(); // Call the callback after updating the cart
        },
    });
}

//increase quantity
$(document).on("click", ".plus-btn", function (e) {
    e.preventDefault();
    let $row = $(this).closest("tr");
    let rowId = $row.data("row_id");
    let updateRoute = $row.data("update_route");
    let $qtyInput = $row.find(".cart-qty");
    let currentQty = parseFloat($qtyInput.val());

    let newQty = currentQty + 1;
    $qtyInput.val(newQty);
    updateCartQuantity(rowId, newQty, updateRoute);
});

//decrease quantity
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
        updateCartQuantity(rowId, newQty, updateRoute);
    }
});

// Cart quantity input field change event
$(document).on("change", ".cart-qty", function () {
    let $row = $(this).closest("tr");
    let rowId = $row.data("row_id");
    let updateRoute = $row.data("update_route");
    let newQty = parseFloat($(this).val());

    // Ensure quantity does not go below 1
    if (newQty >= 0) {
        updateCartQuantity(rowId, newQty, updateRoute);
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
function updateCartQuantity(rowId, newQty, updateRoute) {
    $.ajax({
        url: updateRoute,
        type: "PUT",
        data: {
            rowId: rowId,
            qty: newQty,
        },
        success: function (response) {
            if (response.success) {
                fetchUpdatedCart(calTotalAmount); // Re-fetch the cart and recalculate the total amount
            } else {
                toastr.error(response.message || "Failed to update quantity");
            }
        },
        error: function () {
            toastr.error("Error updating cart quantity");
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
        success: function (response) {
            fetchUpdatedCart(calTotalAmount); // Call calTotalAmount after cart fetch completes
        },
        error: function () {
            console.error("There was an issue clearing the cart.");
        },
    });
}

// product filter by category & manufacturer
$(document).on("change", "#category_id", function (e) {
    fetchProducts();
});

/** Add to cart start **/
let selectedProduct = {};

$(document).on("click", "#single-product", function () {
    showProductModal($(this));
});

function autoOpenModal(id) {
    let element = $("#products-list").find(".single-product." + id);
    showProductModal(element);
}

/** Purchase Modal Calculation Start **/

// Prevent negative input
$("#purchase_inclusive_price, #profit_percent").on("keydown", function (e) {
    if (e.key === "-" || e.keyCode === 189) {
        e.preventDefault();
    }
});

// function updatePrices() {
//     let purchaseExclusivePrice =
//         parseFloat($("#purchase_exclusive_price").val()) || 0;
//     let profitPercent = parseFloat($("#profit_percent").val()) || 0;
//
//     let calculatedSalesPrice =
//         purchaseExclusivePrice + (purchaseExclusivePrice * profitPercent) / 100;
//
//     $("#sales_price").val(formatNumber(calculatedSalesPrice));
//     $("#wholesale_price").val(formatNumber(calculatedSalesPrice));
//     $("#dealer_price").val(formatNumber(calculatedSalesPrice));
// }

function updatePrices() {
    let purchaseExclusivePrice = parseFloat($("#purchase_exclusive_price").val()) || 0;
    let profitPercent = parseFloat($("#profit_percent").val()) || 0;

    // Grab tax info from modal
    let taxRate = parseFloat($("#product-modal").data("tax_rate")) || 0;
    let taxType = $("#product-modal").data("tax_type") || "exclusive";

    let inclusivePrice = 0;

    if (taxType === "inclusive") {
        // Inclusive → exclusive is price / (1 + rate/100)
        purchaseExclusivePrice = purchaseExclusivePrice / (1 + taxRate / 100);
        inclusivePrice = purchaseExclusivePrice * (1 + taxRate / 100);
    } else {
        // Exclusive → just add tax on top
        inclusivePrice = purchaseExclusivePrice * (1 + taxRate / 100);
    }

    // Update input fields
    $("#purchase_exclusive_price").val(formatNumber(purchaseExclusivePrice));
    $("#purchase_inclusive_price").val(formatNumber(inclusivePrice));

    // Profit calc always based on exclusive
    let calculatedSalesPrice = purchaseExclusivePrice + (purchaseExclusivePrice * profitPercent) / 100;

    // Don't update sales_price - let user enter it manually
    // $("#sales_price").val(formatNumber(calculatedSalesPrice));
    $("#wholesale_price").val(formatNumber(calculatedSalesPrice));
    $("#dealer_price").val(formatNumber(calculatedSalesPrice));
}

// Update prices without reformatting the exclusive price field
function updatePricesWithoutExclusive() {
    let purchaseExclusivePrice = parseFloat($("#purchase_exclusive_price").val()) || 0;
    let profitPercent = parseFloat($("#profit_percent").val()) || 0;

    // Profit calc always based on exclusive
    let calculatedSalesPrice = purchaseExclusivePrice + (purchaseExclusivePrice * profitPercent) / 100;

    // Don't update sales_price - let user enter it manually
    // Update wholesale and dealer prices
    $("#wholesale_price").val(formatNumber(calculatedSalesPrice));
    $("#dealer_price").val(formatNumber(calculatedSalesPrice));
}


$("#purchase_exclusive_price").on("input", function () {
    let value = $(this).val();
    
    // Allow empty field
    if (value === '' || value === null) {
        $("#purchase_inclusive_price").val('');
        return;
    }
    
    // Just sync the value, don't format the current field
    let exclusivePrice = parseFloat(value) || 0;
    $("#purchase_inclusive_price").val(value); // Keep same format
    updatePricesWithoutExclusive();
});

$("#purchase_inclusive_price").on("input", function () {
    let value = $(this).val();
    
    // Allow empty field
    if (value === '' || value === null) {
        $("#purchase_exclusive_price").val('');
        return;
    }
    
    // Just sync the value, don't format the current field
    let inclusivePrice = parseFloat(value) || 0;
    $("#purchase_exclusive_price").val(value); // Keep same format
    updatePricesWithoutExclusive();
});

$("#profit_percent").on("input", function () {
    updatePricesWithoutExclusive();
});

/** Purchase Modal Calculation End **/

function showProductModal(element) {
    selectedProduct = {};

    selectedProduct = {
        product_id: element.data("product_id"),
        product_name: element.find(".product_name").text(),
        purchase_inclusive_price: element.data("purchase_inclusive_price"),
        purchase_exclusive_price: element.data("purchase_exclusive_price"),
        sales_price: element.data("sales_price"),
        profit_percent: element.data("profit_percent"),
        wholesale_price: element.data("wholesale_price"),
        dealer_price: element.data("dealer_price"),
        batch_no: element.data("batch_no"),
        stock: element.data("stock"),
        expire_date: element.data("expire_date"),
        product_image: element.data("product_image"),
        product_unit_name: element.data("product_unit_name"),
        tax_rate: element.data("tax_rate"),
        tax_type: element.data("tax_type"),
    };

    // Set modal display values
    $("#product_name").text(selectedProduct.product_name);
    $("#stock").text(selectedProduct.stock);
    $("#purchase_exclusive_price").val(
        selectedProduct.purchase_exclusive_price
    );
    $("#purchase_inclusive_price").val(
        selectedProduct.purchase_inclusive_price
    );
    $("#profit_percent").val(selectedProduct.profit_percent);
    $("#sales_price").val(selectedProduct.sales_price);
    $("#wholesale_price").val(selectedProduct.wholesale_price);
    $("#dealer_price").val(selectedProduct.dealer_price);
    $("#batch_no").val(selectedProduct.batch_no);
    $("#expire_date").val(selectedProduct.expire_date);
    $("#product-modal").data("tax_rate", selectedProduct.tax_rate);
    $("#product-modal").data("tax_type", selectedProduct.tax_type);

    $("#product-modal").modal("show");
}

$(".product-filter").on("submit", function (e) {
    e.preventDefault();
});

let savingLoader =
        '<div class="spinner-border spinner-border-sm text-primary" role="status"><span class="visually-hidden">Loading...</span></div>',
    $purchase_modal_reload = $("#purchase_modal");
$purchase_modal_reload.initFormValidation(),
    // item modal action
    $("#purchase_modal").on("submit", function (e) {
        e.preventDefault();
        let t = $(this).find(".submit-btn"),
            a = t.html();
        let url = $(this).data("route");
        let quantity = parseFloat($("#product_qty").val());
        $purchase_modal_reload.valid() &&
        $.ajax({
            url: url,
            type: "POST",
            data: {
                type: "purchase",
                id: selectedProduct.product_id,
                name: selectedProduct.product_name,
                product_image: selectedProduct.product_image,
                product_unit_name: selectedProduct.product_unit_name,
                quantity: quantity,
                price: parseFloat($("#purchase_inclusive_price").val()),
                purchase_exclusive_price: parseFloat(
                    $("#purchase_exclusive_price").val()
                ),
                purchase_inclusive_price: parseFloat(
                    $("#purchase_inclusive_price").val()
                ),
                profit_percent: parseFloat($("#profit_percent").val()),
                sales_price: parseFloat($("#sales_price").val()) || 0,
                wholesale_price:
                    parseFloat($("#wholesale_price").val()) || 0,
                dealer_price: parseFloat($("#dealer_price").val()),
                batch_no: $("#batch_no").val(),
                expire_date: $("#expire_date").val(),
            },
            beforeSend: function () {
                t.html(savingLoader).attr("disabled", !0);
            },
            success: function (response) {
                t.html(a).removeClass("disabled").attr("disabled", !1);

                if (response.success) {
                    fetchUpdatedCart(calTotalAmount); // Update totals after cart fetch completes
                    $("#product-modal").modal("hide");
                    $("#product_qty").val("");
                } else {
                    toastr.error(
                        response.message || "Failed to add product to cart."
                    );
                }
            },
            error: function (xhr) {
                toastr.error(
                    "An error occurred while adding product to cart."
                );
            },
        });
    });

/** Add to cart End **/

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

    // Calculate subtotal from cart list
    $("#purchase_cart_list tr").each(function () {
        let cart_subtotal = getNumericValue($(this).find(".cart-subtotal").text()) || 0;
        subtotal += cart_subtotal;
    });

    $("#sub_total").text(currencyFormat(subtotal));

    // Vat
    let tax_rate =
        parseFloat($(".tax_select option:selected").data("rate")) || 0;
    let tax_amount = (subtotal * tax_rate) / 100;
    $("#tax_amount").val(tax_amount.toFixed(2));

    // Discount
    let discount_amount = getNumericValue($("#discount_amount").val()) || 0;
    let discount_type = $(".discount_type").val(); // Get the selected discount type


    if (discount_type == "percent") {
        discount_amount = (subtotal * discount_amount) / 100;

        // Ensure percentage discount does not exceed 100%
        if (discount_amount > subtotal) {
            toastr.error("Discount cannot be more than 100% of the subtotal!");
            discount_amount = subtotal; // Cap discount at subtotal
            $("#discount_amount").val(100); // Reset input field to max 100%
        }
    } else {
        if (discount_amount > subtotal) {
            toastr.error("Discount cannot be more than the subtotal!");
            discount_amount = subtotal;
            $("#discount_amount").val(discount_amount);
        }
    }

    //Shipping Charge
    let shipping_charge = getNumericValue($("#shipping_charge").val()) || 0;

    // Total Amount
    let total_amount =
        subtotal + tax_amount + shipping_charge - discount_amount;
    $("#total_amount").text(currencyFormat(total_amount));

    // Receive Amount
    let receive_amount = getNumericValue($("#receive_amount").val()) || 0;
    if (receive_amount < 0) {
        toastr.error("Receive amount cannot be less than 0!");
        receive_amount = 0;
        $("#receive_amount").val(receive_amount);
    }

    // Change Amount
    let change_amount =
        receive_amount > total_amount ? receive_amount - total_amount : 0;
    $("#change_amount").val(change_amount.toFixed(2)); // Numeric value only

    // Due Amount
    let due_amount =
        total_amount > receive_amount ? total_amount - receive_amount : 0;
    $("#due_amount").val(due_amount.toFixed(2)); // Numeric value only
}

calTotalAmount();

// Cancel btn action
$(".cancel-sale-btn").on("click", function (e) {
    e.preventDefault();
    clearCart("purchase");
    $("#receive_amount").val("");
    $(".null_by_reset").val("");
});

$(".product-filter").on("input", ".search-input", function (e) {
    fetchProducts();
});

let isModalAutoOpened = false;

function fetchProducts() {
    var form = $("form.product-filter")[0];
    $.ajax({
        type: "POST",
        url: $(form).attr("action"),
        data: new FormData(form),
        dataType: "json",
        contentType: false,
        cache: false,
        processData: false,
        success: function (res) {
            $("#products-list").html(res.data);

            if (res.total_products && res.product_id && !isModalAutoOpened) {
                autoOpenModal(res.product_id);
                isModalAutoOpened = true;
                $("#purchase_product_search").val("");
            }
        },
    });
}

$("#product-modal").on("hidden.bs.modal", function () {
    isModalAutoOpened = false;
});

document.addEventListener("DOMContentLoaded", function () {
    const sidebarPlan = document.querySelector(".lg-sub-plan");
    const subPlan = document.querySelector(".sub-plan");

    const isActive = window.innerWidth >= 1150;
    $(".side-bar, .section-container").toggleClass("active", isActive);

    if (isActive) {
        sidebarPlan.style.display = "none";
        subPlan.style.display = "block";
    } else {
        sidebarPlan.style.display = "block";
        subPlan.style.display = "none";
    }
});
