"use strict";

$(".common-validation-modal").on("shown.bs.modal", function () {
    $(this)
        .find("form.ajaxform_instant_reload")
        .each(function () {
            $(this).validate();
        });
});

function formatNumber(value) {
    return value % 1 === 0 ? value.toFixed(0) : value.toFixed(2);
}

// Single image preview Start
$(document).on("change", ".file-input-change", function () {
    let prevId = $(this).data("id");
    newPreviewImage(this, prevId);
});

function newPreviewImage(input, prevId) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
            $("#" + prevId).attr("src", e.target.result);
        };
        reader.readAsDataURL(input.files[0]);
    }
}

// Single image preview End

$(".category-edit-btn").on("click", function () {
    var modal = $("#category-edit-modal");

    $("#category_name").val($(this).data("category-name"));
    $("#category_description").val($(this).data("category-description"));

    modal.find("form").attr("action", $(this).data("url"));
});

$(".units-edit-btn").on("click", function () {
    var url = $(this).data("url");
    var unitName = $(this).data("units-name");
    var unitStatus = $(this).data("units-status");

    $("#unit_view_name").val(unitName);
    $("#unit_status").val(unitStatus);

    if (unitStatus == 1) {
        $("#unit_status").prop("checked", true);
        $(".dynamic-text").text("Active");
    } else {
        $("#unit_status").prop("checked", false);
        $(".dynamic-text").text("Deactive");
    }
    $(".unitUpdateForm").attr("action", url);
});

// View Sale Payments Start
$(document).on("click", ".sale-payment-view", function () {
    const saleId = $(this).data("id");
    const $tableBody = $("#sale-payments-data");
    $tableBody.empty();

    let url = $("#sale-payment-view-url").val();
    url = url.replace("SALE_ID", saleId);

    $.get(url, function (res) {
        if (res.payments && res.payments.length > 0) {
            res.payments.forEach((payment) => {
                const row = `<tr>
                    <td>${payment.created_at ?? ""}</td>
                    <td>${payment.ref_code ?? ""}</td>
                    <td>${payment.amount ?? ""}</td>
                    <td>${payment.payment_type ?? ""}</td>
                </tr>`;
                $tableBody.append(row);
            });
        } else {
            $tableBody.append(
                `<tr><td colspan="4" class="text-center text-muted">No payment data available</td></tr>`
            );
        }

        $("#view-sale-payment-modal").modal("show");
    });
});
// View Sale Payments End

// View Purchase Payments Start
$(".purchase-payment-view").on("click", function () {
    const purchaseId = $(this).data("id");
    const $tableBody = $("#purchase-payments-data");
    $tableBody.empty();

    let url = $("#purchase-payment-view-url").val();
    url = url.replace("PURCHASE_ID", purchaseId);

    $.get(url, function (res) {
        if (res.payments && res.payments.length > 0) {
            res.payments.forEach((payment) => {
                const row = `<tr>
                    <td>${payment.created_at ?? ""}</td>
                    <td>${payment.ref_code ?? ""}</td>
                    <td>${payment.amount ?? ""}</td>
                    <td>${payment.payment_type ?? ""}</td>
                </tr>`;
                $tableBody.append(row);
            });
        } else {
            $tableBody.append(
                `<tr><td colspan="4" class="text-center text-muted">No payment data available</td></tr>`
            );
        }

        $("#view-purchase-payment-modal").modal("show");
    });
});
// View Purchase Payments End

/** Product Start */
$(document).on("click", ".product-view", function () {
    $("#product_name").text($(this).data("name"));
    $("#product_code").text($(this).data("code"));
    $("#product_category").text($(this).data("category"));
    $("#product_unit").text($(this).data("unit"));
    $("#product_purchase_price").text($(this).data("purchase-price"));
    $("#product_sale_price").text($(this).data("sale-price"));
    $("#product_wholesale_price").text($(this).data("wholesale-price"));
    $("#product_dealer_price").text($(this).data("dealer-price"));
    $("#product_low_stock").text($(this).data("low-stock"));
    $("#expire_date").text($(this).data("expire-date"));
    $("#product_manufacturer").text($(this).data("manufacturer"));

    // Set product image
    const product_image = $(this).data("image");
    $("#product_image").attr("src", product_image);

    // Handle stock with color
    const stock = $(this).data("stock");
    const lowStock = $(this).data("low-stock");

    const stockElement = $("#product_stock");
    stockElement.removeClass("text-danger text-success");

    if (stock <= lowStock) {
        stockElement.addClass("text-danger");
    } else {
        stockElement.addClass("text-success");
    }

    stockElement.text(stock);
});

// Stock View
$(document).on("click", ".stock-view-data", function () {
    const stocks = $(this).data("stocks");
    const $tableBody = $("#stocks-table-data");
    var canStockPrice = $("#canStockPrice").val() == "1";
    var showExpireDate = $("#show_expire_date").val() == "1";

    $tableBody.empty();

    if (Array.isArray(stocks) && stocks.length > 0) {
        stocks.forEach((batch) => {
            let row = `<tr>
                <td>${batch.batch_no ?? "N/A"}</td>
                <td>${batch.productStock ?? 0}</td>`;

            if (canStockPrice) {
                row += `<td>${batch.purchase_without_tax ?? 0}</td>
                    <td>${batch.purchase_with_tax ?? 0}</td>`;
            }

            row += `<td>${batch.sales_price ?? 0}</td>
                <td>${batch.wholesale_price ?? 0}</td>
                <td>${batch.dealer_price ?? 0}</td>`;

                if (showExpireDate) {
                    row += `<td>${batch.expire_date ?? ""}</td>`;
                }

            row += `</tr>`;

            $tableBody.append(row);
        });
    } else {
        $tableBody.append(
            `<tr><td colspan="7" class="text-center text-muted">No batch data available</td></tr>`
        );
    }

    $("#stock-modal-view").modal("show");
});

//tax calculation
function updatePrices() {
    let taxRate =
        parseFloat($("#tax_id").find(":selected").data("tax_rate")) || 0;
    let exclusivePrice = parseFloat($("#exclusive_price").val()) || 0;
    let profitValue = parseFloat($("#profit_percent").val()) || 0;
    let taxType = $("#tax_type").val();

    // Calculate inclusive purchase price (includes tax)
    let inclusivePrice = exclusivePrice + (exclusivePrice * taxRate) / 100;

    let mrp = exclusivePrice;

    // Add tax if inclusive
    if (taxType === "inclusive") {
        mrp += (exclusivePrice * taxRate) / 100;
    }

    // Apply only profit percent (no option)
    mrp = mrp + (mrp * profitValue) / 100;

    $("#inclusive_price").val(formatNumber(inclusivePrice));
    $("#mrp_price").val(formatNumber(mrp));
}

// Auto-update on input change
$("#tax_id, #tax_type, #exclusive_price, #profit_percent").on(
    "change input",
    updatePrices
);

// Reverse calculation: MRP to profit %
$("#mrp_price").on("input", function () {
    let taxRate =
        parseFloat($("#tax_id").find(":selected").data("tax_rate")) || 0;
    let exclusivePrice = parseFloat($("#exclusive_price").val()) || 0;
    let mrp = parseFloat($("#mrp_price").val()) || 0;
    let taxType = $("#tax_type").val();

    if (exclusivePrice > 0 && mrp > 0) {
        let basePrice = exclusivePrice;

        if (taxType === "inclusive") {
            basePrice += (exclusivePrice * taxRate) / 100;
        }

        let profitPercent = ((mrp - basePrice) / basePrice) * 100;

        $("#profit_percent").val(formatNumber(profitPercent));
    }
});

$("#inclusive_price").on("input", function () {
    let taxRate =
        parseFloat($("#tax_id").find(":selected").data("tax_rate")) || 0;
    let inclusivePrice = parseFloat($(this).val()) || 0;

    let exclusivePrice = inclusivePrice / (1 + taxRate / 100);

    $("#exclusive_price").val(formatNumber(exclusivePrice));

    // Delay user to finish input
    setTimeout(() => {
        updatePrices();
    }, 900);
});

/** Product End */

$(".parties-view-btn").on("click", function () {
    $("#parties_name").text($(this).data("name"));
    $("#parties_phone").text($(this).data("phone"));
    $("#parties_email").text($(this).data("email"));
    $("#parties_type").text($(this).data("type"));
    $("#parties_address").text($(this).data("address"));
    $("#parties_due").text($(this).data("due"));
});

$(".income-categories-edit-btn").on("click", function () {
    var url = $(this).data("url");
    var name = $(this).data("income-categories-name");
    var description = $(this).data("income-categories-description");

    $("#income_categories_view_name").val(name);
    $("#income_categories_view_description").val(description);

    $(".incomeCategoryUpdateForm").attr("action", url);
});

$(".expense-categories-edit-btn").on("click", function () {
    var url = $(this).data("url");
    var expense_name = $(this).data("expense-categories-name");
    var expense_description = $(this).data("expense-categories-description");

    $("#expense_categories_view_name").val(expense_name);
    $("#expense_categories_view_description").val(expense_description);

    $(".expenseCategoryUpdateForm").attr("action", url);
});

$(".incomes-edit-btn").on("click", function () {
    var url = $(this).data("url");
    var income_category_id = $(this).data("income-category-id");
    var incomeAmount = $(this).data("income-amount");
    var incomeFor = $(this).data("income-for");
    var incomePaymentType = $(this).data("income-payment-type");
    var incomePaymentTypeId = $(this).data("income-payment-type-id");
    var incomeReferenceNo = $(this).data("income-reference-no");
    var incomedate = $(this).data("income-date-update");
    var incomenote = $(this).data("income-note");

    $("#income_categoryId").val(income_category_id);
    $("#inc_price").val(incomeAmount);
    $("#inc_for").val(incomeFor);
    if (
        incomePaymentTypeId !== null &&
        incomePaymentTypeId !== undefined &&
        incomePaymentTypeId !== ""
    ) {
        $("#inc_paymentType").val(incomePaymentTypeId);
    } else {
        $("#inc_paymentType").val(incomePaymentType);
    }
    $("#incomeReferenceNo").val(incomeReferenceNo);
    $("#inc_date_update").val(incomedate);
    $("#inc_note").val(incomenote);

    $(".incomeUpdateForm").attr("action", url);
});

$(".expense-edit-btn").on("click", function () {
    var url = $(this).data("url");
    var expenseCategoryId = $(this).data("expense-category-id");
    var expenseAmount = $(this).data("expense-amount");
    var expensePaymentType = $(this).data("expense-payment-type");
    var expensePaymentTypeId = $(this).data("expense-payment-type-id");
    var expenseReferenceNo = $(this).data("expense-reference-no");
    var expenseFor = $(this).data("expense-for");
    var expenseDate = $(this).data("expense-date");
    var expenseNote = $(this).data("expense-note");

    // Set the values in the modal's fields
    $("#expenseCategoryId").val(expenseCategoryId);
    $("#expense_amount").val(expenseAmount);
    if (
        expensePaymentTypeId !== null &&
        expensePaymentTypeId !== undefined &&
        expensePaymentTypeId !== ""
    ) {
        $("#expensePaymentType").val(expensePaymentTypeId);
    } else {
        $("#expensePaymentType").val(expensePaymentType);
    }
    $("#refeNo").val(expenseReferenceNo);
    $("#expe_for").val(expenseFor);
    $("#edit_date_expe").val(expenseDate);
    $("#expenote").val(expenseNote);

    // Update the form action attribute
    $(".expenseUpdateForm").attr("action", url);
});

function showTab(tabId) {
    const tabs = document.querySelectorAll(".tab-item");
    tabs.forEach((tab) => tab.classList.remove("active"));

    const contents = document.querySelectorAll(".tab-content");
    contents.forEach((content) => content.classList.remove("active"));

    document.getElementById(tabId).classList.add("active");
    document
        .querySelector(`[onclick="showTab('${tabId}')"]`)
        .classList.add("active");
}

// Multidelete Start
function updateSelectedCount() {
    var selectedCount = $(".delete-checkbox-item:checked").length;
    $(".selected-count").text(selectedCount);

    if (selectedCount > 0) {
        $(".delete-show").removeClass("d-none");
    } else {
        $(".delete-show").addClass("d-none");
    }
}

$(".select-all-delete").on("click", function () {
    $(".delete-checkbox-item").prop("checked", this.checked);
    updateSelectedCount();
});

$(document).on("change", ".delete-checkbox-item", function () {
    updateSelectedCount();
});

$(".trigger-modal").on("click", function () {
    var dynamicUrl = $(this).data("url");

    $("#dynamic-delete-form").attr("action", dynamicUrl);

    var ids = $(".delete-checkbox-item:checked")
        .map(function () {
            return $(this).val();
        })
        .get();

    if (ids.length === 0) {
        alert("Please select at least one item.");
        return;
    }

    var form = $("#dynamic-delete-form");
    form.find("input[name='ids[]']").remove();
    ids.forEach(function (id) {
        form.append('<input type="hidden" name="ids[]" value="' + id + '">');
    });
});

$(".create-all-delete").on("click", function (event) {
    event.preventDefault();

    var form = $("#dynamic-delete-form");
    form.submit();
});

// Multidelete End

// Collects Due Start
$("#invoiceSelect").on("change", function () {
    const selectedOption = $(this).find("option:selected");
    const dueAmount = selectedOption.data("due-amount");
    const openingDue = selectedOption.data("opening-due");

    if (!selectedOption.val()) {
        $("#totalAmount").val(openingDue);
        $("#dueAmount").val(openingDue);
    } else {
        $("#totalAmount").val(dueAmount);
        $("#dueAmount").val(dueAmount);
    }

    calculateDueChange();
});

$("#paidAmount").on("input", function () {
    calculateDueChange();
});
function calculateDueChange() {
    const payingAmount = parseFloat($("#paidAmount").val()) || 0;
    const totalAmount = parseFloat($("#totalAmount").val()) || 0;

    if (payingAmount > totalAmount) {
        toastr.error("Cannot pay more than due.");
    }

    const updatedDueAmount = totalAmount - payingAmount;
    $("#dueAmount").val(updatedDueAmount >= 0 ? updatedDueAmount : 0);
}
// Collects Due End

//Subscriber view modal
$(".subscriber-view").on("click", function () {
    $(".business_name").text($(this).data("name"));
    $("#image").attr("src", $(this).data("image"));
    $("#category").text($(this).data("category"));
    $("#package").text($(this).data("package"));
    $("#gateway").text($(this).data("gateway"));
    $("#enroll_date").text($(this).data("enroll"));
    $("#expired_date").text($(this).data("expired"));
    $("#manul_attachment").attr("src", $(this).data("manul-attachment"));
});

/** barcode: start **/
$("#product-search").on("keyup click", function () {
    const query = $(this).val().toLowerCase();
    const fetchRoute = $("#fetch-products-route").val();
    // Fetch matching products
    $.ajax({
        url: fetchRoute,
        type: "GET",
        data: { search: query },
        dataType: "json",
        success: function (data) {
            let productList = "";
            if (data.length > 0) {
                data.forEach((product) => {
                    const stock = product.stocks_sum_product_stock ?? 0;
                    productList += `
                            <li
                                class="list-group-item product-item"
                                data-id="${product.id}"
                                data-name="${product.productName}"
                                data-code="${product.productCode}"
                                data-stock="${stock}">
                                ${product.productName} (${product.productCode})
                            </li>`;
                });
            } else {
                productList =
                    '<li class="list-group-item text-danger">No products found.</li>';
            }
            $("#search-results").html(productList).show();
        },
        error: function () {
            console.log("Unable to fetch products. Please try again later.");
        },
    });
});

// Hide search results when clicking outside
$(document).on("click", function (e) {
    if (!$(e.target).closest("#product-search, #search-results").length) {
        $("#search-results").hide();
    }
});

// When a product is selected from the list
$(document).on("click", ".product-item", function () {
    const productId = $(this).data("id");
    const productName = $(this).data("name");
    const productCode = $(this).data("code");
    const productStock = $(this).data("stock");
    const today = new Date().toISOString().split("T")[0];
    // Add the product to the table if not already added
    if (!$('#product-list tr[data-id="' + productId + '"]').length) {
        const newRow = `
            <tr data-id="${productId}">
                <td class="text-start">${productName}</td>
                <td>${productCode}</td>
                <td>${productStock}</td>
                <td class="large-td">
                    <div class="d-flex align-items-center justify-content-center">
                        <button class="incre-decre sub-btn"><i class="fas fa-minus icon"></i></button>
                        <input type="number" name="qty[]" value="1" class="custom-number-input pint-qty" placeholder="0">
                        <button class="incre-decre add-btn"><i class="fas fa-plus icon"></i></button>
                    </div>
                </td>
                <td class="large-td">
                    <input type="date" name="preview_date[]" value="${today}"  class="input-date">
                </td>
                <td>
                    <button class="x-btn remove-btn text-danger">
                        <i class="far fa-times "></i>
                    </button>
                </td>
              <input type="hidden" name="product_ids[]" value="${productId}">
            </tr>`;
        $("#product-list").append(newRow);
    }

    $("#search-results").hide();
    $("#product-search").val("");
});

$(document).on("click", ".remove-btn", function () {
    $(this).closest("tr").remove();
});

// Increase quantity
$(document).on("click", ".add-btn", function (e) {
    e.preventDefault();
    const qtyInput = $(this).siblings(".pint-qty");
    let currentQty = parseInt(qtyInput.val(), 10) || 0;
    qtyInput.val(currentQty + 1);
});

// Decrease quantity
$(document).on("click", ".sub-btn", function (e) {
    e.preventDefault();
    const qtyInput = $(this).siblings(".pint-qty");
    let currentQty = parseInt(qtyInput.val(), 10) || 1;
    if (currentQty > 1) {
        qtyInput.val(currentQty - 1);
    }
});

let $savingLoader1 =
        '<div class="spinner-border spinner-border-sm text-primary" role="status"><span class="visually-hidden">Loading...</span></div>',
    $barcodeForm = $(".barcodeForm");

$barcodeForm.initFormValidation(),
    $(document).on("submit", ".barcodeForm", function (e) {
        e.preventDefault();
        let t = $(this).find("#barcode-preview-btn"),
            a = t.html();

        if ($barcodeForm.valid()) {
            $.ajax({
                type: "POST",
                url: this.action,
                data: new FormData(this),
                dataType: "json",
                contentType: false,
                cache: false,
                processData: false,
                beforeSend: function () {
                    t.html($savingLoader1).attr("disabled", true);
                },
                success: function (e) {
                    t.html(a).removeClass("disabled").attr("disabled", false);

                    if (e.secondary_redirect_url) {
                        // Open the print page and trigger window.print()
                        let printWindow = window.open(
                            e.secondary_redirect_url,
                            "_blank"
                        );

                        if (printWindow) {
                            printWindow.onload = function () {
                                printWindow.print();
                            };
                        }
                    }

                    if (e.redirect) {
                        location.href = e.redirect;
                    }
                },
                error: function (e) {
                    t.html(a).attr("disabled", false);
                },
            });
        }
    });

/** Barcode: end **/

//Tax start
$(".tax-edit-btn").on("click", function () {
    var url = $(this).data("url");
    var name = $(this).data("tax-name");
    var rate = $(this).data("tax-rate");
    var newrate = $(this).data("new-tax-rate");
    var status = $(this).data("tax-status");

    $("#tax_name").val(name);
    $("#tax_rate").val(rate);
    $("#new_tax_rate").val(newrate);
    $("#tax_status").val(status);
    $(".updateTaxForm").attr("action", url);
});
//Tax End

// Product setting dynamic expiry date start
$(".date-type-selector").on("change", function () {
    const target = $(this).data("target");
    const selectedType = $(this).val();

    if (selectedType === "dmy") {
        $("#" + target + "_dmy").show();
        $("#" + target + "_my").hide();
    } else if (selectedType === "my") {
        $("#" + target + "_my").show();
        $("#" + target + "_dmy").hide();
    } else {
        $("#" + target + "_my, #" + target + "_dmy").hide();
    }
});
// Product setting dynamic expiry date end

/** Report Filter: Start **/

// Handle Custom Date Selection
$(".custom-days").on("change", function () {
    let selected = $(this).val();
    let dateFilters = $(".date-filters");

    // Show or hide the date filters based on selection
    if (selected === "custom_date") {
        dateFilters.removeClass("d-none");
    } else {
        dateFilters.addClass("d-none");
    }

    // Trigger the form submission to apply the filters
    $(".report-filter-form").trigger("input");
});
// Report Filter Form Submission
$(".report-filter-form").on("input change", function (e) {
    e.preventDefault();
    let form = $(this);
    let table = form.attr("table");

    $.ajax({
        type: "POST",
        url: form.attr("action"),
        data: new FormData(this),
        dataType: "json",
        contentType: false,
        cache: false,
        processData: false,
        success: function (res) {
            $(table).html(res.data);
            if (res.total_sale !== undefined) {
                $("#total_sale").text(res.total_sale);
            }
            if (res.total_sale_return !== undefined) {
                $("#total_sale_return").text(res.total_sale_return);
            }
            if (res.total_purchase !== undefined) {
                $("#total_purchase").text(res.total_purchase);
            }
            if (res.total_purchase_return !== undefined) {
                $("#total_purchase_return").text(res.total_purchase_return);
            }
            if (res.total_income !== undefined) {
                $("#total_income").text(res.total_income);
            }
            if (res.total_expense !== undefined) {
                $("#total_expense").text(res.total_expense);
            }
            if (res.total_loss !== undefined) {
                $("#total_loss").text(res.total_loss);
            }
            if (res.total_profit !== undefined) {
                $("#total_profit").text(res.total_profit);
            }
            if (res.total_sale_count !== undefined) {
                $("#total_sale_count").text(res.total_sale_count);
            }
            if (res.total_due !== undefined) {
                $("#total_due").text(res.total_due);
            }
            if (res.total_paid !== undefined) {
                $("#total_paid").text(res.total_paid);
            }
        },
    });
});
/** Report Filter: End **/

// When the user clicks on the show/hide icon
$(".hide-show-icon").click(function () {
    let input = $(this).siblings("input");
    let showIcon = $(this).find(".showIcon");
    let hideIcon = $(this).find(".hideIcon");

    input.attr("type", input.attr("type") === "password" ? "text" : "password");

    showIcon.toggleClass("d-none");
    hideIcon.toggleClass("d-none");
});

// Payment Type Edit Start
$(".payment-types-edit-btn").on("click", function () {
    var url = $(this).data("url");
    var PaymentTypeName = $(this).data("payment-types-name");
    var PaymentTypeStatus = $(this).data("payment-types-status");

    $("#PaymentTypeName").val(PaymentTypeName);
    $("#PaymentTypeStatus").val(PaymentTypeStatus);

    $(".paymentTypeUpdateForm").attr("action", url);
});
// Payment Type Edit End

// Medicine Type Edit Start
$(".medicine-types-edit-btn").on("click", function () {
    var url = $(this).data("url");
    var medicineTypeName = $(this).data("medicine-types-name");
    $("#medicineTypeName").val(medicineTypeName);

    $("#medicineTypeUpdateForm").attr("action", url);
});

// Medicine Type Edit End

// Manufacture Edit Start
$(".manufacturer-edit-btn").on("click", function () {
    var url = $(this).data("url");
    var name = $(this).data("name");
    var description = $(this).data("description");
    $("#name").val(name);
    $("#description").val(description);

    $("#manufacturerUpdateForm").attr("action", url);
});

// Manufacture Edit End

// Box Size Edit Start
$(".box-size-edit-btn").on("click", function () {
    var url = $(this).data("url");
    var name = $(this).data("name");
    $("#name").val(name);

    $("#boxSizeUpdateForm").attr("action", url);
});

// Box Size Edit End


// Choices select js start

$(document).ready(function () {
    const choicesMap = new Map();

    $(".choices-select").each(function () {
        const select = this;
        const choicesInstance = new Choices(select, {
            searchEnabled: true,
            itemSelectText: "",
            shouldSort: false,
        });
        choicesMap.set(select.id, choicesInstance);
    });

    $(document).on("keydown", ".choices__input--cloned", function (e) {
        if (e.key === "Enter") {
            e.preventDefault();

            const activeInput = $(this);
            const searchTerm = activeInput.val().trim();

            if (!searchTerm) return;
            const choicesContainer = activeInput.closest('.choices');

            const selectId = choicesContainer.attr('class').split(' ').find(cls => cls.startsWith('choices-'))?.replace('choices-', '');

            let selectElement = selectId ? $('#' + selectId) : choicesContainer.siblings('select.choices-select');

            if (!selectElement.length) {
                selectElement = $('.choices-select');
            }

            if (!selectElement.length) return;

            const finalSelectId = selectElement.attr('id');

            const isCustomer = finalSelectId === 'party_id';
            const isSupplier = finalSelectId === 'supplier_id';

            if (!isCustomer && !isSupplier) return;

            let matchFound = false;

            selectElement.find("option").each(function () {
                const optionText = $(this).text().trim().toLowerCase();
                if (optionText.includes(searchTerm.toLowerCase())) {
                    matchFound = true;
                    return false;
                }
            });

            if (!matchFound) {
                const modalId = isCustomer ? '#customer-create-modal' : '#supplier-create-modal';
                const modalNameInput = $(modalId).find('input[name="name"]');
                const modalPhoneInput = $(modalId).find('input[name="phone"]');

                if (!modalNameInput.length) return;

                // Check if search term is a phone number
                const phoneRegex = /^(\+?[0-9]{1,15}|[0-9]{3,})$/;
                const isPhoneNumber = phoneRegex.test(searchTerm);

                selectElement.val('').trigger("change");

                if (isPhoneNumber && modalPhoneInput.length) {
                    modalPhoneInput.val(searchTerm);
                } else {
                    modalNameInput.val(searchTerm);
                }

                new bootstrap.Modal($(modalId)[0]).show();
                activeInput.val('');
            }
        }
    });
});

/** multiple payment type add dynamically start **/
let paymentTypes = [];
$("#payment_type option").each(function () {
    paymentTypes.push({ id: $(this).val(), name: $(this).text() });
});

// Delete button SVG
const deleteBtnSVG = `
<button type="button" class="delete-btn">
  <svg width="19" height="18" viewBox="0 0 19 18" fill="none" xmlns="http://www.w3.org/2000/svg">
    <path d="M15.4519 4.12476L14.9633 11.6436C14.8384 13.5646 14.7761 14.5251 14.2699 15.2157C14.0195 15.5571 13.6974 15.8452 13.3236 16.0618C12.5678 16.4998 11.5561 16.4998 9.53271 16.4998C7.50669 16.4998 6.49366 16.4998 5.73733 16.0609C5.3634 15.844 5.04108 15.5554 4.79088 15.2134C4.28485 14.5217 4.2238 13.5598 4.10171 11.6362L3.625 4.12476" stroke="#C52127" stroke-width="1.25" stroke-linecap="round"/>
    <path d="M7.1731 8.80115H11.9039" stroke="#C52127" stroke-width="1.25" stroke-linecap="round"/>
    <path d="M8.35571 11.7405H10.7211" stroke="#C52127" stroke-width="1.25" stroke-linecap="round"/>
    <path d="M2.44238 4.12524H16.6347M12.7361 4.12524L12.1979 3.06904C11.8404 2.36744 11.6615 2.01663 11.3532 1.79785C11.2848 1.74932 11.2124 1.70615 11.1366 1.66877C10.7951 1.50024 10.3853 1.50024 9.56558 1.50024C8.72532 1.50024 8.30522 1.50024 7.95806 1.67583C7.88112 1.71475 7.8077 1.75967 7.73856 1.81012C7.4266 2.03777 7.25234 2.40141 6.90383 3.12869L6.42627 4.12524" stroke="#C52127" stroke-width="1.25" stroke-linecap="round"/>
  </svg>
</button>
`;

// Generate select HTML dynamically
function generatePaymentSelect(index) {
    let options = paymentTypes.map(pt => `<option value="${pt.id}">${pt.name}</option>`).join('');
    return `<select name="payment_types[${index}][payment_type_id]" class="form-select">${options}</select>`;
}

// Attach delete button
function attachDelete(paymentGrid) {
    paymentGrid.find(".delete-btn").off("click").on("click", function () {
        paymentGrid.remove();
        checkRestoreOriginalSelect();
        updateReceiveAmountFromGrids();
    });
}

// Attach amount input handler
function attachAmountChange(amountInput) {
    amountInput.on("input", function () {
        updateReceiveAmountFromGrids();
    });
}

// Update receive_amount from grids
function updateReceiveAmountFromGrids() {
    let grids = $(".payment-main-container .payment-grid");
    let total = 0;
    grids.each(function () {
        total += parseFloat($(this).find(".amount").val()) || 0;
    });
    $("#receive_amount").val(total).trigger("input");
}

// Restore original select if all dynamic grids removed
function checkRestoreOriginalSelect() {
    let paymentMain = $(".payment-main-container");
    let grids = paymentMain.find(".payment-grid");

    let dynamicGrids = grids.filter(function () {
        return $(this).find(".delete-btn").length > 0;
    });

    if (dynamicGrids.length === 0) {
        $("#payment_type").show();
        $("#receive_amount").prop("readonly", false);
        grids.remove();
    }
}

// Add payment button click
$(document).on("click", ".add-payment-btn", function (e) {
    e.preventDefault();
    let paymentMain = $(".payment-main-container");
    let existingCount = paymentMain.find(".payment-grid").length;

    if (existingCount === 0) {
        $("#payment_type").hide();
        for (let i = 0; i < 2; i++) {
            let deleteButton = i === 0 ? '' : deleteBtnSVG;
            let paymentGrid = $(`
                <div class="payment-grid">
                    ${generatePaymentSelect(i)}
                    <input name="payment_types[${i}][amount]" class="amount form-control" type="number" step="any" min="0" value="0">
                    ${deleteButton}
                </div>
            `);
            paymentMain.append(paymentGrid);
            attachDelete(paymentGrid);
            attachAmountChange(paymentGrid.find(".amount"));
        }

        let receiveVal = parseFloat($("#receive_amount").val()) || 0;
        paymentMain.find(".payment-grid").first().find(".amount").val(receiveVal);
        $("#receive_amount").prop("readonly", true);
        updateReceiveAmountFromGrids();
        return;
    }

    // Add new dynamic grid
    let index = paymentMain.find(".payment-grid").length;
    let paymentGrid = $(`
        <div class="payment-grid">
            ${generatePaymentSelect(index)}
            <input name="payment_types[${index}][amount]" class="amount form-control" type="number" step="any" min="0" value="0">
            ${deleteBtnSVG}
        </div>
    `);

    paymentMain.append(paymentGrid);
    attachDelete(paymentGrid);
    attachAmountChange(paymentGrid.find(".amount"));
    updateReceiveAmountFromGrids();
});

// On page load: for edit mode
$(document).ready(function () {
    let paymentMain = $(".payment-main-container");
    let existingGrids = paymentMain.find(".payment-grid");

    if (existingGrids.length > 0) {
        $("#payment_type").hide();
        $("#receive_amount").prop("readonly", true);

        existingGrids.each(function (i) {
            $(this).find("select").attr("name", `payment_types[${i}][payment_type_id]`);
            $(this).find("input.amount").attr("name", `payment_types[${i}][amount]`);

            if (i === 0) {
                $(this).find(".delete-btn").remove(); // first grid no delete
            }

            attachDelete($(this));
            attachAmountChange($(this).find(".amount"));
        });

        updateReceiveAmountFromGrids();
    }
});

// View Sale Payments Start
$(".sale-payment-view").on("click", function () {
    const saleId = $(this).data("id");
    const $tableBody = $("#sale-payments-data");
    $tableBody.empty();

    let url = $("#sale-payment-view-url").val();
    url = url.replace("SALE_ID", saleId);

    $.get(url, function (res) {
        if (res.payments && res.payments.length > 0) {
            res.payments.forEach((payment) => {
                const row = `<tr>
                    <td>${payment.created_at ?? ""}</td>
                    <td>${payment.ref_code ?? ""}</td>
                    <td>${payment.amount ?? ""}</td>
                    <td>${payment.payment_type ?? ""}</td>
                </tr>`;
                $tableBody.append(row);
            });
        } else {
            $tableBody.append(
                `<tr><td colspan="4" class="text-center text-muted">No payment data available</td></tr>`
            );
        }

        $("#view-sale-payment-modal").modal("show");
    });
});
// View Sale Payments End

// View Purchase Payments Start
$(".purchase-payment-view").on("click", function () {
    const purchaseId = $(this).data("id");
    const $tableBody = $("#purchase-payments-data");
    $tableBody.empty();

    let url = $("#purchase-payment-view-url").val();
    url = url.replace("PURCHASE_ID", purchaseId);

    $.get(url, function (res) {
        if (res.payments && res.payments.length > 0) {
            res.payments.forEach((payment) => {
                const row = `<tr>
                    <td>${payment.created_at ?? ""}</td>
                    <td>${payment.ref_code ?? ""}</td>
                    <td>${payment.amount ?? ""}</td>
                    <td>${payment.payment_type ?? ""}</td>
                </tr>`;
                $tableBody.append(row);
            });
        } else {
            $tableBody.append(
                `<tr><td colspan="4" class="text-center text-muted">No payment data available</td></tr>`
            );
        }

        $("#view-purchase-payment-modal").modal("show");
    });
});
// View Purchase Payments End

// Choices select js end

$(document).on("click", ".stock-get", function () {
    var url = $("#stock-data-get").val();

    $.ajax({
        url: url,
        method: "GET",
        success: function (res) {
            let table = $(".table-stock");
            table.empty();

            if (res.stocks.length === 0) {
                table.append(
                    `<tr><td colspan="6">No stock products found</td></tr>`
                );
            } else {
                $.each(res.stocks, function (index, product) {
                    let totalQty = 0;
                    let stockValue = 0;
                    let purchasePrice = 0;
                    let salePrice = 0;

                    $.each(product.stocks, function (i, itemStock) {
                        totalQty += itemStock.productStock;
                        stockValue += itemStock.productStock * itemStock.purchase_with_tax;
                        purchasePrice += itemStock.purchase_with_tax; // you can calculate average if needed
                        salePrice += itemStock.sales_price;           // sum or average as per your logic
                    });

                    // Optionally, you can calculate the average price per product
                    let avgPurchase = product.stocks.length ? (purchasePrice / product.stocks.length) : 0;
                    let avgSale = product.stocks.length ? (salePrice / product.stocks.length) : 0;

                    table.append(`
                        <tr>
                            <td>${index + 1}</td>
                            <td class="text-start">${product.productName ?? ""}</td>
                            <td>${currencyFormat(avgPurchase)}</td>
                            <td class="${totalQty <= product.alert_qty ? "text-danger" : "text-success"}">${totalQty}</td>
                            <td>${currencyFormat(avgSale)}</td>
                            <td>${currencyFormat(stockValue)}</td>
                        </tr>
                    `);
                });
            }
        },
        error: function () {
            alert("Something went wrong!");
        },
    });
});


$(document).on("keyup", ".stock-search", function () {
    let value = $(this).val().toLowerCase();
    $(".table-stock tr").filter(function () {
        $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
    });
});

// Activate Bootstrap tab based on URL hash
document.addEventListener('DOMContentLoaded', function () {
    const hash = window.location.hash; // e.g., #product

    if (hash) {
        const tabTriggerEl = document.querySelector('button[data-bs-target="' + hash + '"]');
        if (tabTriggerEl) {
            const tab = new bootstrap.Tab(tabTriggerEl);
            tab.show();
        }
    }
});


const calendarBtn = document.getElementById("calendarBtn");
const datePicker = document.getElementById("datePicker");

calendarBtn.addEventListener("click", () => {
    datePicker.click();
});


