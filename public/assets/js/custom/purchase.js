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

// Local storage helpers for purchase cart persistence
const PURCHASE_CART_CACHE_KEY = "purchase_cart_cache";

// Restore cart from cache on load
$(document).ready(function () {
    restoreCartFromCache();
});

function saveCartCache() {
    const items = [];
    $("#purchase_cart_list tr.product-cart-tr").each(function () {
        const $row = $(this);
        items.push({
            product_id: $row.data("product_id"),
            name: $row.data("product_name"),
            product_image: $row.data("product_image"),
            product_unit_name: $row.data("product_unit_name") || "",
            quantity:
                parseFloat($row.data("product_qty")) ||
                parseFloat($row.find(".cart-qty").val()) ||
                0,
            invoice_qty: parseFloat($row.data("invoice_qty")) || 0,
            bonus_qty: parseFloat($row.data("bonus_qty")) || 0,
            pack_size: $row.data("pack_size") || null,
            pack_qty: parseFloat($row.data("pack_qty")) || 0,
            gross_total_price: parseFloat($row.data("gross_total_price")) || 0,
            vat_amount: parseFloat($row.data("vat_amount")) || 0,
            discount_amount: parseFloat($row.data("discount_amount")) || 0,
            purchase_exclusive_price:
                parseFloat($row.data("purchase_exclusive_price")) || 0,
            purchase_inclusive_price:
                parseFloat($row.data("purchase_inclusive_price")) || 0,
            profit_percent: parseFloat($row.data("profit_percent")) || 0,
            sales_price: parseFloat($row.data("sales_price")) || 0,
            wholesale_price: parseFloat($row.data("wholesale_price")) || 0,
            dealer_price: parseFloat($row.data("dealer_price")) || 0,
            batch_no: $row.data("batch_no") || "",
            expire_date: $row.data("expire_date") || "",
        });
    });

    localStorage.setItem(PURCHASE_CART_CACHE_KEY, JSON.stringify(items));
}

function clearCartCache() {
    localStorage.removeItem(PURCHASE_CART_CACHE_KEY);
}

function afterCartUpdate() {
    calTotalAmount();
    saveCartCache();
}

async function restoreCartFromCache() {
    try {
        const cached = localStorage.getItem(PURCHASE_CART_CACHE_KEY);
        if (!cached) return;

        const items = JSON.parse(cached) || [];
        if (!items.length) return;

        // If server already has cart items, don't rehydrate
        if ($("#purchase_cart_list tr.product-cart-tr").length > 0) return;

        const addUrl = $("#purchase_modal").data("route");
        if (!addUrl) return;

        for (const item of items) {
            await $.ajax({
                url: addUrl,
                type: "POST",
                data: {
                    type: "purchase",
                    id: item.product_id,
                    name: item.name,
                    product_image: item.product_image,
                    product_unit_name: item.product_unit_name || "",
                    quantity: item.quantity,
                    invoice_qty: item.invoice_qty,
                    bonus_qty: item.bonus_qty,
                    pack_size: item.pack_size,
                    pack_qty: item.pack_qty,
                    gross_total_price: item.gross_total_price,
                    vat_amount: item.vat_amount,
                    discount_amount: item.discount_amount,
                    price: item.purchase_inclusive_price,
                    purchase_exclusive_price: item.purchase_exclusive_price,
                    purchase_inclusive_price: item.purchase_inclusive_price,
                    profit_percent: item.profit_percent,
                    sales_price: item.sales_price,
                    wholesale_price: item.wholesale_price,
                    dealer_price: item.dealer_price,
                    batch_no: item.batch_no,
                    expire_date: item.expire_date,
                },
            });
        }

        // Refresh UI and recalc totals
        fetchUpdatedCart(afterCartUpdate);
    } catch (error) {
        console.error("Failed to restore purchase cart from cache", error);
    }
}

// Format number for display
function formatNumber(value, decimals = 2) {
    if (value === null || value === undefined || value === '') return '';
    return Number.isInteger(+value) ? parseInt(value) : (+value).toFixed(decimals);
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
            saveCartCache();
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
                fetchUpdatedCart(afterCartUpdate);
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
                fetchUpdatedCart(afterCartUpdate); // Re-fetch the cart and recalculate the total amount
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
            fetchUpdatedCart(afterCartUpdate); // Call calTotalAmount after cart fetch completes
            clearCartCache();
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
let isEditMode = false;
let editCartRowId = null;

$(document).on("click", "#single-product", function () {
    isEditMode = false;
    editCartRowId = null;
    showProductModal($(this));
});

// Edit cart item
$(document).on("click", ".edit-cart-btn", function (e) {
    e.preventDefault();
    let $row = $(this).closest("tr");
    isEditMode = true;
    editCartRowId = $row.data("row_id");
    showCartItemInModal($row);
});

function showCartItemInModal($row) {
    selectedProduct = {
        product_id: $row.data("product_id"),
        product_name: $row.data("product_name"),
        product_image: $row.data("product_image"),
        product_box_size: $row.data("product_box_size"),
    };

    // Set modal display values
    $("#product_name").text(selectedProduct.product_name);
    $("#stock").text($row.data("stock") || 0);
    
    // Handle pack size dropdown - show all box sizes
    if (window.allBoxSizes && window.allBoxSizes.length > 0) {
        $("#pack_row_container").show();
        
        let packSizeOptions = '<option value="">Select Box Size</option>';
        let currentPackSize = $row.data("pack_size") || "";
        window.allBoxSizes.forEach(function(boxSize) {
            let selected = (boxSize.value == currentPackSize) ? ' selected' : '';
            packSizeOptions += `<option value="${boxSize.value}"${selected}>${boxSize.name}</option>`;
        });
        $("#pack_size").html(packSizeOptions);
        $("#pack_qty").val($row.data("pack_qty") || 0);
    } else {
        $("#pack_row_container").hide();
        $("#pack_size").val("");
        $("#pack_qty").val(0);
    }
    
    // Populate all fields with existing cart data
    $("#invoice_qty").val($row.data("invoice_qty") || 0);
    $("#bonus_qty").val($row.data("bonus_qty") || 0);
    $("#product_qty").val($row.data("product_qty") || 0);
    $("#gross_total_price").val($row.data("gross_total_price") || 0);
    $("#vat_amount").val($row.data("vat_amount") || 0);
    $("#product_discount_amount").val($row.data("discount_amount") || 0);
    $("#purchase_exclusive_price").val($row.data("purchase_exclusive_price") || 0);
    $("#purchase_inclusive_price").val($row.data("purchase_inclusive_price") || 0);
    $("#profit_percent").val($row.data("profit_percent") || 0);
    $("#sales_price").val($row.data("sales_price") || 0);
    $("#wholesale_price").val($row.data("wholesale_price") || 0);
    $("#dealer_price").val($row.data("dealer_price") || 0);
    $("#batch_no").val($row.data("batch_no") || "");
    $("#expire_date").val($row.data("expire_date") || "");

    // Trigger calculation to update Net Total display
    updatePurchasePrices();

    $("#product-modal").modal("show");
}

function autoOpenModal(id) {
    let element = $("#products-list").find(".single-product." + id);
    isEditMode = false;
    editCartRowId = null;
    showProductModal(element);
}

/** Purchase Modal Calculation Start **/

// Prevent negative input
$("#purchase_inclusive_price, #profit_percent, #invoice_qty, #bonus_qty, #gross_total_price, #vat_amount, #product_discount_amount").on("keydown", function (e) {
    if (e.key === "-" || e.keyCode === 189) {
        e.preventDefault();
    }
});

// Calculate invoice_qty from pack_size * pack_qty, then product_qty from invoice_qty + bonus_qty
function updateInvoiceQtyFromPack() {
    let packSize = parseFloat($("#pack_size").val()) || 0;
    let packQty = parseFloat($("#pack_qty").val()) || 0;
    
    if (packSize > 0 && packQty > 0) {
        let invoiceQty = packSize * packQty;
        $("#invoice_qty").val(invoiceQty);
    }
    
    updateProductQty();
}

// Calculate product_qty from invoice_qty + bonus_qty
function updateProductQty() {
    let invoiceQty = parseFloat($("#invoice_qty").val()) || 0;
    let bonusQty = parseFloat($("#bonus_qty").val()) || 0;
    let productQty = invoiceQty + bonusQty;
    
    $("#product_qty").val(productQty);
    
    // Recalculate unit price when quantity changes
    updatePurchasePrices();
}

// Calculate Net Total and Unit Prices
function updatePurchasePrices() {
    // Use modal context to avoid conflicts with main form fields
    let $modal = $("#product-modal");
    let grossTotalPrice = parseFloat($modal.find("#gross_total_price").val()) || 0;
    let vatAmount = parseFloat($modal.find("#vat_amount").val()) || 0;
    let discountAmount = parseFloat($modal.find("#product_discount_amount").val()) || 0;
    let productQty = parseFloat($modal.find("#product_qty").val()) || 0;
    
    // console.log('updatePurchasePrices called:');
    // console.log('Gross Total:', grossTotalPrice);
    // console.log('VAT:', vatAmount);
    // console.log('Discount:', discountAmount);
    // console.log('Product Qty:', productQty);
    
    // Net Total = (Gross Total + VAT) - Discount
    let netTotal = (grossTotalPrice + vatAmount) - discountAmount;
    console.log('Calculated Net Total:', netTotal);
    
    // Display Net Total
    $modal.find("#net_total_display").text(formatNumber(netTotal));
    console.log('Display updated to:', formatNumber(netTotal));
    
    // Unit Price = Net Total / Product Qty
    let unitPrice = 0;
    if (productQty > 0) {
        unitPrice = netTotal / productQty;
    }
    console.log('Unit Price:', unitPrice);
    
    // Update purchase prices (exclusive = inclusive for now)
    $modal.find("#purchase_exclusive_price").val(formatNumber(unitPrice));
    $modal.find("#purchase_inclusive_price").val(formatNumber(unitPrice));
    
    // Update profit percent and prices
    updateProfitPercent();
}

// Calculate profit percent from purchase price and sale price
function updateProfitPercent() {
    let purchaseExclusivePrice = parseFloat($("#purchase_exclusive_price").val()) || 0;
    let salesPrice = parseFloat($("#sales_price").val()) || 0;
    
    let profitPercent = 0;
    if (purchaseExclusivePrice > 0) {
        profitPercent = ((salesPrice - purchaseExclusivePrice) / purchaseExclusivePrice) * 100;
    }
    
    $("#profit_percent").val(formatNumber(profitPercent));
    
    // Sync wholesale and dealer prices with sales price
    $("#wholesale_price").val(formatNumber(salesPrice));
    $("#dealer_price").val(formatNumber(salesPrice));
}

// Calculate sale price from profit percent
function updateSalePriceFromProfit() {
    let purchaseExclusivePrice = parseFloat($("#purchase_exclusive_price").val()) || 0;
    let profitPercent = parseFloat($("#profit_percent").val()) || 0;
    
    let calculatedSalesPrice = purchaseExclusivePrice + (purchaseExclusivePrice * profitPercent) / 100;
    
    $("#sales_price").val(formatNumber(calculatedSalesPrice));
    $("#wholesale_price").val(formatNumber(calculatedSalesPrice));
    $("#dealer_price").val(formatNumber(calculatedSalesPrice));
}

// Event listeners for pack fields
$("#pack_size, #pack_qty").on("change input", function () {
    updateInvoiceQtyFromPack();
});

// Event listeners for quantity fields
$("#invoice_qty, #bonus_qty").on("input", function () {
    updateProductQty();
});

// Event listeners for price calculation fields - scope to modal to avoid ID conflicts
$("#product-modal").on("input", "#gross_total_price, #vat_amount, #product_discount_amount", function () {
    updatePurchasePrices();
});

// Event listener for profit percent
$("#profit_percent").on("input", function () {
    updateSalePriceFromProfit();
});

// Event listener for sales price - recalculate profit percent
$("#sales_price").on("input", function () {
    let salesPrice = parseFloat($(this).val()) || 0;
    
    // Sync wholesale and dealer prices
    $("#wholesale_price").val(formatNumber(salesPrice));
    $("#dealer_price").val(formatNumber(salesPrice));
    
    // Recalculate profit percent
    let purchaseExclusivePrice = parseFloat($("#purchase_exclusive_price").val()) || 0;
    let profitPercent = 0;
    if (purchaseExclusivePrice > 0) {
        profitPercent = ((salesPrice - purchaseExclusivePrice) / purchaseExclusivePrice) * 100;
    }
    $("#profit_percent").val(formatNumber(profitPercent));
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
        product_box_size: element.data("product_box_size"),
    };

    // Set modal display values
    $("#product_name").text(selectedProduct.product_name);
    $("#stock").text(selectedProduct.stock);
    
    // Handle pack size dropdown - show all box sizes
    if (window.allBoxSizes && window.allBoxSizes.length > 0) {
        $("#pack_row_container").show();
        
        let packSizeOptions = '<option value="">Select Box Size</option>';
        window.allBoxSizes.forEach(function(boxSize) {
            let selected = (boxSize.value == selectedProduct.product_box_size) ? ' selected' : '';
            packSizeOptions += `<option value="${boxSize.value}"${selected}>${boxSize.name}</option>`;
        });
        $("#pack_size").html(packSizeOptions);
        $("#pack_qty").val(0);
    } else {
        $("#pack_row_container").hide();
        $("#pack_size").val("");
        $("#pack_qty").val(0);
    }
    
    // Initialize new fields with defaults
    $("#invoice_qty").val(0);
    $("#bonus_qty").val(0);
    $("#product_qty").val(0);
    $("#gross_total_price").val(0);
    $("#vat_amount").val(0);
    $("#discount_amount").val(0);
    
    $("#purchase_exclusive_price").val(
        selectedProduct.purchase_exclusive_price
    );
    $("#purchase_inclusive_price").val(
        selectedProduct.purchase_inclusive_price
    );
    $("#profit_percent").val(selectedProduct.profit_percent);
    $("#sales_price").val(selectedProduct.sales_price);
    $("#wholesale_price").val(selectedProduct.sales_price);
    $("#dealer_price").val(selectedProduct.sales_price);
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
        
        let quantity = parseFloat($("#product_qty").val());
        let invoiceQty = parseFloat($("#invoice_qty").val()) || 0;
        let bonusQty = parseFloat($("#bonus_qty").val()) || 0;
        let grossTotalPrice = parseFloat($("#gross_total_price").val()) || 0;
        let vatAmount = parseFloat($("#vat_amount").val()) || 0;
        let discountAmount = parseFloat($("#product_discount_amount").val()) || 0;
        
        if (!$purchase_modal_reload.valid()) return;
        
        // Edit mode - update existing cart item
        if (isEditMode && editCartRowId) {
            let $row = $('tr[data-row_id="' + editCartRowId + '"]');
            let updateUrl = $row.data("update_route");
            
            $.ajax({
                url: updateUrl,
                type: "PUT",
                data: {
                    rowId: editCartRowId,
                    qty: quantity,
                    invoice_qty: invoiceQty,
                    bonus_qty: bonusQty,
                    pack_size: parseFloat($("#pack_size").val()) || null,
                    pack_qty: parseFloat($("#pack_qty").val()) || 0,
                    gross_total_price: grossTotalPrice,
                    vat_amount: vatAmount,
                    discount_amount: discountAmount,
                    purchase_exclusive_price: parseFloat($("#purchase_exclusive_price").val()),
                    purchase_inclusive_price: parseFloat($("#purchase_inclusive_price").val()),
                    profit_percent: parseFloat($("#profit_percent").val()),
                    sales_price: parseFloat($("#sales_price").val()) || 0,
                    wholesale_price: parseFloat($("#wholesale_price").val()) || 0,
                    dealer_price: parseFloat($("#dealer_price").val()) || 0,
                    batch_no: $("#batch_no").val(),
                    expire_date: $("#expire_date").val(),
                },
                beforeSend: function () {
                    t.html(savingLoader).attr("disabled", !0);
                },
                success: function (response) {
                    t.html(a).removeClass("disabled").attr("disabled", !1);
                    if (response.success) {
                        toastr.success("Cart item updated successfully!");
                        fetchUpdatedCart(afterCartUpdate);
                        $("#product-modal").modal("hide");
                        resetModalForm();
                    } else {
                        toastr.error(response.message || "Failed to update cart item.");
                    }
                },
                error: function (xhr) {
                    t.html(a).removeClass("disabled").attr("disabled", !1);
                    toastr.error("An error occurred while updating cart item.");
                },
            });
        } else {
            // Add mode - add new item to cart
            let url = $(this).data("route");
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
                    invoice_qty: invoiceQty,
                    bonus_qty: bonusQty,
                    pack_size: parseFloat($("#pack_size").val()) || null,
                    pack_qty: parseFloat($("#pack_qty").val()) || 0,
                    gross_total_price: grossTotalPrice,
                    vat_amount: vatAmount,
                    discount_amount: discountAmount,
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
                        fetchUpdatedCart(afterCartUpdate);
                        $("#product-modal").modal("hide");
                        resetModalForm();
                    } else {
                        toastr.error(
                            response.message || "Failed to add product to cart."
                        );
                    }
                },
                error: function (xhr) {
                    t.html(a).removeClass("disabled").attr("disabled", !1);
                    toastr.error(
                        "An error occurred while adding product to cart."
                    );
                },
            });
        }
    });

// Reset modal form
function resetModalForm() {
    isEditMode = false;
    editCartRowId = null;
    $("#product_qty").val("");
    $("#invoice_qty").val(0);
    $("#bonus_qty").val(0);
    $("#pack_size").val("");
    $("#pack_qty").val(0);
    $("#pack_row_container").hide();
    $("#gross_total_price").val(0);
    $("#vat_amount").val(0);
    $("#product_discount_amount").val(0);
    $("#net_total_display").text("0");
}

/** Add to cart End **/

// Trigger calculation whenever Discount, or Receive Amount fields change
$("#discount_amount, #receive_amount, #shipping_charge").on(
    "input",
    function () {
        calTotalAmount();
    }
);

// discount calculation
$(".discount_type").on("change", function () {
    calTotalAmount();
});

// Function to calculate the total amount
function calTotalAmount() {
    let subtotal = 0;
    let productWiseVat = 0;
    let productWiseDiscount = 0;

    // Calculate subtotal and product-wise vat/discount from cart list
    // NOTE: The subtotal already includes product-wise VAT/Discount effects
    // because Unit Price = (Gross Total + VAT - Discount) / Qty
    $("#purchase_cart_list tr.product-cart-tr").each(function () {
        let $row = $(this);
        let cart_subtotal = getNumericValue($row.find(".cart-subtotal").text()) || 0;
        
        // Read product-wise VAT and Discount for display purposes only
        let vat = parseFloat($row.attr("data-vat_amount")) || 0;
        let discount = parseFloat($row.attr("data-discount_amount")) || 0;
        
        subtotal += cart_subtotal;
        productWiseVat += vat;
        productWiseDiscount += discount;
    });

    $("#sub_total").text(currencyFormat(subtotal));
    
    // Display product-wise VAT (for information only)
    $("#product_wise_vat_display").val(productWiseVat.toFixed(2));

    // Bill-wise Discount calculation
    let bill_discount_input = getNumericValue($("#discount_amount").val()) || 0;
    let discount_type = $(".discount_type").val();
    let bill_discount_amount = 0;

    // Only validate discount if there are items in cart (subtotal > 0)
    if (subtotal > 0) {
        if (discount_type == "percent") {
            bill_discount_amount = (subtotal * bill_discount_input) / 100;

            // Ensure percentage discount does not exceed 100%
            if (bill_discount_amount > subtotal) {
                toastr.error("Discount cannot be more than 100% of the subtotal!");
                bill_discount_amount = subtotal;
                $("#discount_amount").val(100);
            }
        } else {
            bill_discount_amount = bill_discount_input;
            if (bill_discount_amount > subtotal) {
                toastr.error("Discount cannot be more than the subtotal!");
                bill_discount_amount = subtotal;
                $("#discount_amount").val(bill_discount_amount);
            }
        }
    }

    // Display total discount (product-wise + bill-wise) for information
    let total_discount_display = productWiseDiscount + bill_discount_amount;
    $("#total_discount_display").val(total_discount_display.toFixed(2));

    // Total Amount = Subtotal - Bill Discount
    // (No bill-wise VAT, no shipping charge)
    let total_amount = subtotal - bill_discount_amount;
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
    $("#discount_amount").val("");
    $("#total_discount_display").val("");
    $("#product_wise_vat_display").val("");
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
    resetModalForm();
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
