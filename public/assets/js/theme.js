(function ($) {
    "use strict";

    sideManu();

    function sideManu() {
        let manuStor = $(".side-bar").html();

        $(".side-bar").html("<div class='overlay'></div>" + manuStor);
        $(".sidebar-opner").on("click ", function () {
            $(".side-bar, .section-container").toggleClass("active");
        });
        $(".side-bar .close-btn, .side-bar .overlay").on("click ", function () {
            $(".side-bar, .section-container").toggleClass("active");
        });

        $("li>ul").toggleClass("dropdown-menu");

        let animationSpeed = 300;

        let subMenuSelector = ".dropdown-menu";

        $(".side-bar-manu > ul").on("click", ".dropdown a", function (e) {
            let $this = $(this);
            let checkElement = $this.next();

            if (
                checkElement.is(subMenuSelector) &&
                checkElement.is(":visible")
            ) {
                checkElement.slideUp(animationSpeed, function () {
                    checkElement.removeClass("menu-open");
                });
                checkElement.parent("li").removeClass("active");
            }

            //If the menu is not visible
            else if (
                checkElement.is(subMenuSelector) &&
                !checkElement.is(":visible")
            ) {
                //Get the parent menu
                let parent = $this.parents("ul").first();
                //Close all open menus within the parent
                let ul = parent.find("ul:visible").slideUp(animationSpeed);
                //Remove the menu-open class from the parent
                ul.removeClass("menu-open");
                //Get the parent li
                let parent_li = $this.parent("li");

                //Open the target menu and add the menu-open class
                checkElement.slideDown(animationSpeed, function () {
                    //Add the class active to the parent li
                    checkElement.addClass("menu-open");
                    parent.find("li.active").removeClass("active");
                    parent_li.addClass("active");
                });
            }
            //if this isn't a link, prevent the page from being redirected
            if (checkElement.is(subMenuSelector)) {
                e.preventDefault();
            }
        });

        // show sidebar in previous menu
        var sidebar = $('.side-bar');

        // Restore scroll position on page load
        var savedScroll = localStorage.getItem('sidebar-scroll');
        if (savedScroll !== null) {
            sidebar.scrollTop(savedScroll);
        }

        // Save scroll position before leaving the page
        $(window).on('beforeunload', function() {
            localStorage.setItem('sidebar-scroll', sidebar.scrollTop());
        });
    }

    // photo upload preview
    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $(".image-preview").attr("src", e.target.result);
                $(".image-preview").hide();
                $(".image-preview").fadeIn(650);
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    $("#add-profile").on("change", function () {
        readURL(this);
        $(".image-preview-icon").addClass("d-none");
    });

    $("#feature-btn").on("click", function (e) {
        e.preventDefault();

        let value = $(".add-feature").val();
        let featureCount = $(".feature-list").children().length;

        if (value !== "") {
            $(".feature-list").append(`
            <div class="col-lg-6 mt-4 remove-list feature-item">
                <div class="feature-wrp">
                    <div class="form-control d-flex justify-content-between align-items-center">
                        <input name="features[features_${featureCount}][]" required class="features-dy" type="text" value="${value}">
                       <div class='d-flex align-items-center gap-4'>
                        <label class="switch m-0">
                            <input type="checkbox" checked value="1" name="features[features_${featureCount}][]">
                            <span class="slider round"></span>
                        </label>
                        <svg class="delete-feature" width="20" height="22" viewBox="0 0 20 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M2.5 5.58342H17.5M13.3796 5.58342L12.8107 4.40986C12.4328 3.6303 12.2438 3.24051 11.9179 2.99742C11.8457 2.9435 11.7691 2.89553 11.689 2.854C11.3281 2.66675 10.8949 2.66675 10.0286 2.66675C9.1405 2.66675 8.6965 2.66675 8.32957 2.86185C8.24826 2.90509 8.17066 2.955 8.09758 3.01106C7.76787 3.264 7.5837 3.66804 7.21535 4.47613L6.71061 5.58342" stroke="#F54336" stroke-width="1.5" stroke-linecap="round"/>
                            <path d="M16.25 5.5835L15.7336 13.9377C15.6016 16.0722 15.5357 17.1394 15.0007 17.9067C14.7361 18.2861 14.3956 18.6062 14.0006 18.8468C13.2017 19.3335 12.1325 19.3335 9.99392 19.3335C7.8526 19.3335 6.78192 19.3335 5.98254 18.8459C5.58733 18.6049 5.24667 18.2842 4.98223 17.9042C4.4474 17.1357 4.38287 16.0669 4.25384 13.9295L3.75 5.5835" stroke="#F54336" stroke-width="1.5" stroke-linecap="round"/>
                            <path d="M7.5 10.7717H12.5" stroke="#F54336" stroke-width="1.5" stroke-linecap="round"/>
                            <path d="M8.75 14.1467H11.25" stroke="#F54336" stroke-width="1.5" stroke-linecap="round"/>
                        </svg>
                        </div>
                    </div>
                </div>
            </div>
            `);
            $(".add-feature").val("");
        }
    });
    $(document).on("click", ".delete-feature", function () {
        $(this).closest(".feature-item").remove();
    });
})(jQuery);

// Multiple image upload

document.addEventListener("DOMContentLoaded", () => {
    // Track existing images that user keeps
    let existingImages = [];

    // Store initial images shown in preview
    $(".multiple-preview-container .image-item img").each(function () {
        const src = $(this).attr("src");
        if (src && !src.startsWith("data:")) {
            existingImages.push(src);
        }
    });


    // Handle delete button for existing images
    $(document).on("click", ".multiple-preview-container .delete-button", function () {
        const imageItem = $(this).closest(".image-item");
        const img = imageItem.find("img");
        const src = img.attr("src");

        // If it's an existing image (not just uploaded)
        if (!src.startsWith("blob:") && !src.startsWith("data:")) {
            existingImages = existingImages.filter(s => s !== src);

            // Add hidden input for removed image
            const input = $('<input>')
                .attr("type", "hidden")
                .attr("name", "deleted_images[]")
                .val(src);
            $(".multiple-image-upload").append(input);
        }

        // Remove image preview from UI
        imageItem.remove();
    });

    $(document).on("change", ".multiple-file-input", function () {
        const fileInput = this;
        const imageContainer = $(fileInput)
            .closest(".multiple-image-upload")
            .find(".multiple-preview-container")
            .get(0);

        handleFileSelect(fileInput, imageContainer);
    });

    function handleFileSelect(fileInput, imageContainer) {
        // Clear the preview container
        imageContainer.innerHTML = "";

        const files = Array.from(fileInput.files);
        const dt = new DataTransfer();

        files.forEach((file, index) => {
            const reader = new FileReader();

            reader.onload = function (e) {
                const imgElement = document.createElement("img");
                imgElement.src = e.target.result;

                const deleteButton = document.createElement("button");
                deleteButton.type = "button";
                deleteButton.textContent = "X";
                deleteButton.className = "delete-button";

                const imageItem = document.createElement("div");
                imageItem.className = "image-item";
                imageItem.appendChild(imgElement);
                imageItem.appendChild(deleteButton);

                imageContainer.appendChild(imageItem);

                dt.items.add(file);

                deleteButton.addEventListener("click", function () {
                    const newDt = new DataTransfer();
                    Array.from(dt.files).forEach((f, i) => {
                        if (i !== index) newDt.items.add(f);
                    });

                    fileInput.files = newDt.files;
                    handleFileSelect(fileInput, imageContainer);
                });
            };

            reader.readAsDataURL(file);
        });

        fileInput.files = dt.files;
    }

});


document.addEventListener("DOMContentLoaded", function () {
    const toggleBtn = document.querySelector(".menu-opener");
    const sidebarPlan = document.querySelector(".lg-sub-plan");
    const subPlan = document.querySelector(".sub-plan");

    toggleBtn.addEventListener("click", function () {
        if (sidebarPlan.style.display === "none") {
            sidebarPlan.style.display = "block";
            subPlan.style.display = "none";
        } else {
            sidebarPlan.style.display = "none";
            subPlan.style.display = "block";
        }
    });
});

document.addEventListener("DOMContentLoaded", function () {
    const toggleBtn = document.getElementById("toggleNotification");
    const container = document.getElementById("notificationContainer");

    toggleBtn.addEventListener("click", function (e) {
        e.stopPropagation(); // prevent click from bubbling
        container.style.display =
            container.style.display === "block" ? "none" : "block";
    });

    document.addEventListener("click", function (e) {
        const wrapper = document.getElementById("notificationWrapper");
        if (!wrapper.contains(e.target)) {
            container.style.display = "none";
        }
    });
});

document.addEventListener("DOMContentLoaded", function () {
    const printBtn = document.querySelector(".print-window");
    if (printBtn) {
        printBtn.addEventListener("click", function (e) {
            e.preventDefault();
            window.print();
        });
    }
});
