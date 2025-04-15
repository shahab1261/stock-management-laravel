// Home Testimonial Slider

var swiper = new Swiper(".mySwiperCustomer", {
    slidesPerView: "auto",
    speed: 1000,

    pagination: {
        el: ".swiper-pagination",
        clickable: true,
    },
    spaceBetween: 10,
    navigation: {
        nextEl: "#swiperCustomerNext",
        prevEl: "#swiperCustomerPrev",
    },
    breakpoints: {
        320: {
            slidesPerView: 1,
            spaceBetween: 10,
        },
        640: {
            slidesPerView: 1,
            spaceBetween: 10,
        },
        768: {
            slidesPerView: 1,
            spaceBetween: 10,
        },
        1024: {
            slidesPerView: 1,
            spaceBetween: 20,
        },
    },
});

const navbarIconSearch = document.getElementById("navbar-searchicon-sm");
const navbarSearchInput =
    document.getElementsByClassName("navbar-seaarch-sm")[0];

navbarIconSearch.addEventListener("click", () => {
    navbarSearchInput.classList.toggle("search-show-hide");
});

// product detail slider
let thumbnails = document.getElementsByClassName("thumbnail");
let activeImages = document.getElementsByClassName("active");

for (var i = 0; i < thumbnails.length; i++) {
    thumbnails[i].addEventListener("mouseover", function () {
        console.log(activeImages);

        if (activeImages.length > 0) {
            activeImages[0].classList.remove("active");
        }

        this.classList.add("active");

        document.getElementById("featured").src = this.src;
    });
}

let buttonRight = document.getElementById("slideRight");
let buttonLeft = document.getElementById("slideLeft");

buttonLeft.addEventListener("click", function () {
    document.getElementById("slider").scrollLeft -= 180;
});

buttonRight.addEventListener("click", function () {
    document.getElementById("slider").scrollLeft += 180;
});

// Product Detials Zoom

// document selector

// const mainImage = document.querySelector(".mainImage");
// console.log(mainImage);
// mainImage.addEventListener("mousemove", (e) => {
//   console.log(e.pageX);
//   console.log(mainImage.offsetHeight);
//   const containerWidth = mainImage.offsetWidth;
//   const containerHeight = mainImage.offsetHeight;

//   const image = mainImage.querySelector("img");
//   const imageWidth = image.offsetWidth;
//   const imageHeight = image.offsetHeight;

//   const x = e.pageX - mainImage.offsetLeft;
//   const y = e.pageY - mainImage.offsetTop;

//   const translateX = (containerWidth / 2 - x) * 2;
//   const translateY = (containerHeight / 2 - y) * 2;

//   const scale = 3;

//   image.style.transform = `translate(${translateX}px, ${translateY}px) scale(${scale})`;
// });
// mainImage.addEventListener("mouseleave", (e) => {
//   const image = mainImage.querySelector("img");
//   image.style.transform = "translate(0%, 0%) scale(1)";
// });

////////////////////////////////// Product Range Slider

//////////////////////////////////////// User Dashboard

// document.addEventListener("DOMContentLoaded", function() {
//   const mainImage = document.querySelector(".mainImage");
//   let isDragging = false;
//   let initialX, initialY, initialTranslateX, initialTranslateY;

//   // Zoom effect on mouse move
//   mainImage.addEventListener("mousemove", (e) => {
//       if (!isDragging) {
//           const containerWidth = mainImage.offsetWidth;
//           const containerHeight = mainImage.offsetHeight;
//           const image = mainImage.querySelector("img");
//           const imageWidth = image.offsetWidth;
//           const imageHeight = image.offsetHeight;

//           const x = e.pageX - mainImage.offsetLeft;
//           const y = e.pageY - mainImage.offsetTop;

//           const translateX = (containerWidth / 2 - x) * 2;
//           const translateY = (containerHeight / 2 - y) * 2;

//           const scale = 3;

//           image.style.transform = `translate(${translateX}px, ${translateY}px) scale(${scale})`;
//       }
//   });

//   mainImage.addEventListener("mouseleave", (e) => {
//       if (!isDragging) {
//           const image = mainImage.querySelector("img");
//           image.style.transform = "translate(0%, 0%) scale(1)";
//       }
//   });

//   // Touch-enabled zooming
//   mainImage.addEventListener("touchstart", startDrag);
//   mainImage.addEventListener("touchmove", drag);
//   mainImage.addEventListener("touchend", endDrag);

//   function startDrag(e) {
//       e.preventDefault();
//       const touch = e.touches[0];
//       initialX = touch.clientX;
//       initialY = touch.clientY;
//       isDragging = true;

//       const image = mainImage.querySelector("img");
//       const transform = window.getComputedStyle(image).transform;
//       const matrix = new WebKitCSSMatrix(transform);
//       initialTranslateX = matrix.m41;
//       initialTranslateY = matrix.m42;
//   }

//   function drag(e) {
//       e.preventDefault();
//       if (isDragging) {
//           const touch = e.touches[0];
//           const currentX = touch.clientX;
//           const currentY = touch.clientY;
//           const deltaX = currentX - initialX;
//           const deltaY = currentY - initialY;
//           const image = mainImage.querySelector("img");
//           image.style.transform = `translate(${initialTranslateX + deltaX}px, ${initialTranslateY + deltaY}px) scale(3)`;
//       }
//   }

//   function endDrag() {
//       isDragging = false;
//   }
// });

// new
document.addEventListener("DOMContentLoaded", function () {
    const mainImage = document.querySelector(".mainImage");
    let isDragging = false;
    let initialX, initialY, initialTranslateX, initialTranslateY;
    let lastScale = 1;

    // Zoom effect on mouse move (for large screens)
    if (window.innerWidth >= 768) {
        const image = mainImage.querySelector("img");
        mainImage.addEventListener("mousemove", (e) => {
            const containerWidth = mainImage.offsetWidth;
            const containerHeight = mainImage.offsetHeight;
            const imageWidth = image.offsetWidth;
            const imageHeight = image.offsetHeight;

            const x = e.pageX - mainImage.offsetLeft;
            const y = e.pageY - mainImage.offsetTop;

            const translateX = (containerWidth / 2 - x) * 2;
            const translateY = (containerHeight / 2 - y) * 2;

            const scale = 3;

            image.style.transform = `translate(${translateX}px, ${translateY}px) scale(${scale})`;
        });

        mainImage.addEventListener("mouseleave", (e) => {
            image.style.transform = "translate(0%, 0%) scale(1)";
        });
    }

    // Touch-enabled zooming and panning (for mobile)
    mainImage.addEventListener("touchstart", startDrag);
    mainImage.addEventListener("touchmove", drag);
    mainImage.addEventListener("touchend", endDrag);

    function startDrag(e) {
        e.preventDefault();
        if (e.touches.length === 1) {
            const touch = e.touches[0];
            initialX = touch.clientX;
            initialY = touch.clientY;
            isDragging = true;

            const image = mainImage.querySelector("img");
            const transform = window.getComputedStyle(image).transform;
            const matrix = new WebKitCSSMatrix(transform);
            initialTranslateX = matrix.m41;
            initialTranslateY = matrix.m42;
        }
    }

    function drag(e) {
        e.preventDefault();
        if (isDragging && e.touches.length === 1) {
            const touch = e.touches[0];
            const currentX = touch.clientX;
            const currentY = touch.clientY;
            const deltaX = currentX - initialX;
            const deltaY = currentY - initialY;
            const image = mainImage.querySelector("img");
            image.style.transform = `translate(${initialTranslateX + deltaX}px, ${
                initialTranslateY + deltaY
            }px) scale(${lastScale})`;
        }
    }

    function endDrag() {
        isDragging = false;
    }

    // Pinch-to-zoom functionality (for mobile)
    mainImage.addEventListener("gesturestart", function (e) {
        e.preventDefault();
        lastScale = 1;
    });

    mainImage.addEventListener("gesturechange", function (e) {
        e.preventDefault();
        lastScale = e.scale;
        const image = mainImage.querySelector("img");
        const transform = window.getComputedStyle(image).transform;
        const matrix = new WebKitCSSMatrix(transform);
        image.style.transform = `translate(${initialTranslateX}px, ${initialTranslateY}px) scale(${lastScale})`;
    });

    mainImage.addEventListener("gestureend", function (e) {
        e.preventDefault();
    });
});
