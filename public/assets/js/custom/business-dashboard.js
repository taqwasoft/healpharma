"use strict";

// currency format
function currencyFormat(amount, type = "icon", decimals = 2) {
    let symbol = $("#currency_symbol").val();
    let position = $("#currency_position").val();
    let code = $("#currency_code").val();

    let formattedAmount = formatNumber(amount, decimals); // Abbreviate number

    // Apply currency format based on the position and type
    if (type === "icon" || type === "symbol") {
        return position === "right" ? formattedAmount + symbol : symbol + formattedAmount;
    } else {
        return position === "right" ? formattedAmount + " " + code : code + " " + formattedAmount;
    }
}
// Update design when a single business content exists
document.addEventListener("DOMContentLoaded", function () {
    // Select the container, ensure it exists
    const container = document.querySelector(".business-stat");
    if (container) {
        const businessContents = container.querySelectorAll(".business-content");
        const customImageBg = document.querySelector(".custom-image-bg");

        // Dynamically set column class based on the number of business content elements
        container.classList.add(`columns-${businessContents.length}`);

        if (businessContents.length === 1) {
            businessContents[0].style.padding = "3% 2%";
            if (customImageBg) {
                customImageBg.style.padding = "2%";
            }
            businessContents[0].style.borderRadius = "0";
        }
    }
});

function renderArrow(selector, direction) {
    let up_icon = `<svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
    <path d="M4.27325 10.6666H11.7266C11.8584 10.666 11.9872 10.6264 12.0965 10.5527C12.2058 10.479 12.2908 10.3745 12.3408 10.2525C12.3907 10.1304 12.4034 9.99635 12.3771 9.86714C12.3508 9.73794 12.2869 9.61941 12.1932 9.52657L8.47325 5.80657C8.41127 5.74409 8.33754 5.69449 8.2563 5.66065C8.17506 5.6268 8.08792 5.60937 7.99991 5.60938C7.91191 5.60937 7.82477 5.6268 7.74353 5.66065C7.66229 5.69449 7.58856 5.74409 7.52658 5.80657L3.80658 9.52657C3.71297 9.61941 3.64898 9.73794 3.62273 9.86714C3.59647 9.99635 3.60912 10.1304 3.65907 10.2525C3.70902 10.3745 3.79403 10.479 3.90335 10.5527C4.01267 10.6264 4.1414 10.666 4.27325 10.6666Z" fill="#00987F"/>
    </svg>
    `;
    let down_icon = `<svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
    <path d="M4.27325 5.33344H11.7266C11.8584 5.33399 11.9872 5.37362 12.0965 5.44733C12.2058 5.52104 12.2908 5.62551 12.3408 5.74753C12.3907 5.86955 12.4034 6.00365 12.3771 6.13286C12.3508 6.26206 12.2869 6.38059 12.1932 6.47343L8.47325 10.1934C8.41127 10.2559 8.33754 10.3055 8.2563 10.3394C8.17506 10.3732 8.08792 10.3906 7.99991 10.3906C7.91191 10.3906 7.82477 10.3732 7.74353 10.3394C7.66229 10.3055 7.58856 10.2559 7.52658 10.1934L3.80658 6.47343C3.71297 6.38059 3.64898 6.26206 3.62273 6.13286C3.59647 6.00365 3.60912 5.86955 3.65907 5.74753C3.70902 5.62551 3.79403 5.52104 3.90335 5.44733C4.01267 5.37362 4.1414 5.33399 4.27325 5.33344Z" fill="#F44236"/>
    </svg>`;

    let parentDiv = $('.dynamic-color');
    parentDiv.removeClass('up down');

    let iconHtml = "";
    if (direction === "up") {
        iconHtml = up_icon;
        parentDiv.addClass('up');
    } else if (direction === "down") {
        iconHtml = down_icon;
        parentDiv.addClass('down');
    } else {
      iconHtml = up_icon;
      parentDiv.addClass('up');
    }
    $(selector).html(iconHtml);
  }

  function formatNumber(number, decimals = 2) {
    if (number >= 1e9) {
        return removeTrailingZeros((number / 1e9).toFixed(decimals)) + "B";
    } else if (number >= 1e6) {
        return removeTrailingZeros((number / 1e6).toFixed(decimals)) + "M";
    } else if (number >= 1e3) {
        return removeTrailingZeros((number / 1e3).toFixed(decimals)) + "K";
    } else {
        return removeTrailingZeros(number.toFixed(decimals));
    }
}

function removeTrailingZeros(value) {
    return parseFloat(value).toString();
}

getDashboardData();

function getDashboardData() {
    var url = $("#get-dashboard").val();
    $.ajax({
        type: "GET",
        url: url,
        dataType: "json",
        success: function (res) {
            $("#total_customer").text(res.total_customer);
            $("#today_customer").text(res.today_customer);
            $("#customer_percentage").text(res.customer_percentage);
            $("#total_supplier").text(res.total_supplier);
            $("#today_supplier").text(res.today_supplier);
            $("#supplier_percentage").text(res.supplier_percentage);
            $("#total_stock").text(res.total_stock);
            $("#today_stock").text(res.today_stock);
            $("#stock_percentage").text(res.stock_percentage);
            $("#total_expire").text(res.total_expire);
            $("#today_expire").text(res.today_expire);
            $("#expire_percentage").text(res.expire_percentage);

            renderArrow("#customer_arrow", res.customer_arrow);
            renderArrow("#supplier_arrow", res.supplier_arrow);
            renderArrow("#stock_arrow", res.stock_arrow);
            renderArrow("#expire_arrow", res.expire_arrow);
        },
    });
}


const canvas = document.getElementById("todayReportChart");
const ctxOverallReports = canvas.getContext("2d");

const gradient1 = ctxOverallReports.createLinearGradient(0, 0, 0, 250);
gradient1.addColorStop(0, "#883DCF");
gradient1.addColorStop(1, "#CFB1EC");

const gradient2 = ctxOverallReports.createLinearGradient(0, 0, 0, 250);
gradient2.addColorStop(0, "#2BB2FE");
gradient2.addColorStop(1, "#AAE0FF");

const gradient3 = ctxOverallReports.createLinearGradient(0, 0, 0, 250);
gradient3.addColorStop(0, "#F9C80E");
gradient3.addColorStop(1, "#FDE99F");

const gradient4 = ctxOverallReports.createLinearGradient(0, 0, 0, 250);
gradient4.addColorStop(0, "#EB3D4D");
gradient4.addColorStop(1, "#F7B1B8");

const Overallreports = new Chart(ctxOverallReports, {
    type: "doughnut",
    data: {
        labels: ["Sales", "Purchase", "Income", "Expense"],
        datasets: [
            {
                data: [0, 0, 0, 0],
                backgroundColor: [gradient1, gradient2, gradient3, gradient4],
                borderWidth: 0,
                hoverOffset: 0,
                cutout: "90%",
                borderRadius: 20,
                spacing: 5,
            },
        ],
    },
    options: {
        plugins: {
            tooltip: {
                backgroundColor: "#ffffff",
                titleColor: "#111827",
                bodyColor: "#374151",
                borderColor: "#e5e7eb",
                borderWidth: 1,
                callbacks: {
                    label: function (context) {
                        return `${context.label} : $${context.formattedValue}`;
                    },
                },
            },
            legend: {
                display: false,
            },
        },
    },
});

function fetchTaskData(year = new Date().getFullYear()) {
    const url = $("#get-overall-report").val() + "?year=" + year;
    $.ajax({
        url: url,
        method: "GET",
        success: function (response) {
            Overallreports.data.datasets[0].data = [
                response.overall_sale || 0.000001,
                response.overall_purchase || 0.000001,
                response.overall_income || 0.000001,
                response.overall_expense || 0.000001,
            ];
            Overallreports.update();

            $("#today_loss_profit").text(currencyFormat(response.today_loss_profit));
            $("#overall_sale").text(currencyFormat(response.overall_sale));
            $("#overall_purchase").text(currencyFormat(response.overall_purchase));
            $("#overall_income").text(currencyFormat(response.overall_income));
            $("#overall_expense").text(currencyFormat(response.overall_expense));
        },
        error: function (error) {
            console.error("Error fetching data:", error);
        },
    });
}

fetchTaskData();

// Handle year change
$(".overall-year").on("change", function () {
    const year = $(this).val();
    fetchTaskData(year);
});

// Handle resize
window.addEventListener("resize", function () {
    Overallreports.resize();
});



// Function to get yearly statistics and update the chart
function getLossProfitYear(year = new Date().getFullYear()) {
    const url = $("#loss-profit-data").val() + "?year=" + year;

    $.ajax({
        type: "GET",
        url: url,
        dataType: "json",
        success: function (res) {
            const loss = res.loss;
            const profit = res.profit;
            const total_loss = [];
            const total_profit = [];

            for (let i = 1; i <= 12; i++) {
                const monthName = getMonthNameFromIndex(i);

                const lossDataForMonth = loss.filter((item) => item.month === monthName);
                total_loss[i - 1] = lossDataForMonth.reduce((sum, item) => sum + item.total, 0);

                const profitDataForMonth = profit.filter((item) => item.month === monthName);
                total_profit[i - 1] = profitDataForMonth.reduce((sum, item) => sum + item.total, 0);
            }

            // Update chart with the new data
            totalLossProfitChart(total_loss, total_profit);

            const loss_value = total_loss.reduce(
                (sum, value) => sum + value,
                0
            );
            const profit_value = total_profit.reduce(
                (sum, value) => sum + value,
                0
            );

            document.querySelector(
                ".loss-value"
            ).textContent = `${currencyFormat(loss_value)}`;
            document.querySelector(
                ".profit-value"
            ).textContent = `${currencyFormat(profit_value)}`;
        },
        error: function (err) {
            console.error("Error fetching data:", err);
        },
    });
}

// Function to convert month index to month name
function getMonthNameFromIndex(index) {
    const months = [
        "January",
        "February",
        "March",
        "April",
        "May",
        "June",
        "July",
        "August",
        "September",
        "October",
        "November",
        "December",
    ];
    return months[index - 1];
}

// Initial chart load with the current year data
getLossProfitYear();

// Handle year change event
$(".loss-profit-year").on("change", function () {
    const year = $(this).val();
    getLossProfitYear(year);
});

let lossProfitChart;
const  ctxlossprofit = document.getElementById("profitLossChart").getContext("2d");
function totalLossProfitChart(total_loss, total_profit) {
    if (lossProfitChart) {
        lossProfitChart.destroy();
    }

    const months = [
        "Jan",
        "Feb",
        "Mar",
        "Apr",
        "May",
        "Jun",
        "Jul",
        "Aug",
        "Sep",
        "Oct",
        "Nov",
        "Dec",
    ];

    // Create gradients
    const profitGradient = ctxlossprofit.createLinearGradient(0, 0, 0, 400);
    profitGradient.addColorStop(0, "#22CAAD");
    profitGradient.addColorStop(1, "#2BB2FE");

    const lossGradient = ctxlossprofit.createLinearGradient(0, 0, 0, 400);
    lossGradient.addColorStop(0, "#F9C80E");
    lossGradient.addColorStop(1, "#F86624");

    let hoveredIndex = null;

     lossProfitChart = new Chart(ctxlossprofit, {
        type: "bar",
        data: {
            labels: months,
            datasets: [
                {
                    label: "Profit",
                    data: total_profit,
                    backgroundColor: profitGradient,
                    borderRadius: 12,
                    barPercentage: 0.4,
                },
                {
                    label: "Loss",
                    data: total_loss,
                    backgroundColor: lossGradient,
                    borderRadius: 12,
                    barPercentage: 0.4,
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                tooltip: {
                    backgroundColor: "#ffffff",
                    titleColor: "#000",
                    bodyColor: "#000",
                    borderColor: "#ddd",
                    borderWidth: 1,
                    padding: 10,
                    callbacks: {
                        label: function (context) {
                            return `${context.dataset.label} : $${context.formattedValue}`;
                        },
                    },
                },
                legend: {
                    display: false,
                    labels: {
                        color: "#444",
                    },
                },
            },
            scales: {
                y: {
                    ticks: {
                        callback: (value) =>
                            `$${value >= 1000 ? value / 1000 + "k" : value}`,
                        color: "#777",
                    },
                    grid: {
                        drawBorder: false,
                        color: "#C2C6CE",
                        borderDash: [4, 4],
                    },
                },
                x: {
                    ticks: {
                        color: (context) =>
                            context.index === hoveredIndex ? "#00987F" : "#777",
                        font: (context) =>
                            context.index === hoveredIndex
                                ? { weight: "bold", size: 12 }
                                : { weight: "normal", size: 12 },
                    },
                    grid: {
                        display: false,
                    },
                },
            },
            onHover: (event, chartElements) => {
                if (chartElements.length > 0) {
                    const index = chartElements[0].index;
                    if (hoveredIndex !== index) {
                        hoveredIndex = index;
                        lossProfitChart.update();
                    }
                } else {
                    if (hoveredIndex !== null) {
                        hoveredIndex = null;
                        lossProfitChart.update();
                    }
                }
            },
        },
    });
}


// Function to get yearly statistics and update the chart
function getSalePurchaseYear(year = new Date().getFullYear()) {
    const url = $("#sale-purchase-data").val() + "?year=" + year;

    $.ajax({
        type: "GET",
        url: url,
        dataType: "json",
        success: function (res) {
            const sale = res.sale;
            const purchase = res.purchase;
            const total_sale = [];
            const total_purchase = [];

            for (let i = 1; i <= 12; i++) {
                const monthName = getMonthNameFromIndex(i);

                const saleDataForMonth = sale.filter((item) => item.month === monthName);
                total_sale[i - 1] = saleDataForMonth.reduce((sum, item) => sum + item.total, 0);

                const purchaseDataForMonth = purchase.filter((item) => item.month === monthName);
                total_purchase[i - 1] = purchaseDataForMonth.reduce((sum, item) => sum + item.total, 0);
            }

            // Update chart with the new data
            totalsalePurchaseChart(total_sale, total_purchase);

            const sale_value = total_sale.reduce((sum, value) => sum + value, 0);
            const purchase_value = total_purchase.reduce((sum, value) => sum + value, 0);

            document.querySelector(".sale-value").textContent = `${currencyFormat(sale_value)}`;
            document.querySelector(".purchase-value").textContent = `${currencyFormat(purchase_value)}`;
        },
        error: function (err) {
            console.error("Error fetching data:", err);
        },
    });
}

// Initial chart load with the current year data
getSalePurchaseYear();

// Handle year change event
$(".sale-purchase-year").on("change", function () {
    const year = $(this).val();
    getSalePurchaseYear(year);
});


let salePurchaseChart;
const chartCanvas = document.getElementById("salesChart");
const ctxpurchasesales = chartCanvas.getContext("2d");
function totalsalePurchaseChart(total_sale, total_purchase) {
        if (salePurchaseChart) {
            salePurchaseChart.destroy();
        }
    const purchasesBgGradient = ctxpurchasesales.createLinearGradient(
        0,
        0,
        0,
        chartCanvas.height
    );
    purchasesBgGradient.addColorStop(0, "rgba(42, 180, 249, 0.17)");
    purchasesBgGradient.addColorStop(1, "rgba(35, 198, 188, 0.0250976)");
    purchasesBgGradient.addColorStop(1, "rgba(34, 201, 177, 0)");

    const salesBgGradient = ctxpurchasesales.createLinearGradient(
        0,
        0,
        0,
        chartCanvas.height
    );
    salesBgGradient.addColorStop(0, "rgba(248, 107, 35, 0.12)");
    salesBgGradient.addColorStop(1, "rgba(249, 190, 16, 0)");

    const purchasesGradient = ctxpurchasesales.createLinearGradient(
        0,
        0,
        chartCanvas.width,
        0
    );
    purchasesGradient.addColorStop(0, "#2BB4FA");
    purchasesGradient.addColorStop(1, "#23C8B3");

    const salesGradient = ctxpurchasesales.createLinearGradient(
        0,
        0,
        chartCanvas.width,
        0
    );
    salesGradient.addColorStop(0, "#F87122");
    salesGradient.addColorStop(1, "#F9BB11");

    const labels = [
        "Jan",
        "Feb",
        "Mar",
        "Apr",
        "May",
        "Jun",
        "Jul",
        "Aug",
        "Sep",
        "Oct",
        "Nov",
        "Dec",
    ];


    salePurchaseChart =  new Chart(ctxpurchasesales, {
        type: "line",
        data: {
            labels: labels,
            datasets: [
                {
                    label: "Purchases",
                    data: total_purchase,
                    borderColor: purchasesGradient,
                    backgroundColor: purchasesBgGradient,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: "#2BB4FA",
                    pointRadius: 0,
                    pointHoverRadius: 5,
                },
                {
                    label: "Sales",
                    data: total_sale,
                    borderColor: salesGradient,
                    backgroundColor: salesBgGradient,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: "#F87122",
                    pointRadius: 0,
                    pointHoverRadius: 5,
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: "index",
                intersect: false,
            },
            plugins: {
                tooltip: {
                    backgroundColor: "#ffffff",
                    titleColor: "#000000",
                    bodyColor: "#000000",
                    borderColor: "#e5e7eb",
                    borderWidth: 1,
                    callbacks: {
                        label: function (context) {
                            return `${context.dataset.label} : $${context.formattedValue}`;
                        },
                    },
                },
                legend: {
                    display: false,
                },
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        drawBorder: false,
                        color: "#C2C6CE",
                        borderDash: [4, 4],
                    },
                    ticks: {
                        callback: (value) =>
                            `$${value >= 1000 ? value / 1000 + "k" : value}`,
                    },
                },
                x: {
                    grid: {
                        display: false,
                    },
                },
            },
        },
    });
}

  document.addEventListener("DOMContentLoaded", function () {
    const closeBtn = document.querySelector("#demoAlert .btn-close");
    const alertBox = document.getElementById("demoAlert");

    closeBtn.addEventListener("click", function () {
      alertBox.style.display = "none";
    });
  });
