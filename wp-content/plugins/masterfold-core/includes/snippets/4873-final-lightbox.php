<?php
/**
 * FINAL LIGHTBOX
 *
 * Moved from WPCode snippet #4873 (location: everywhere).
 * Change: only printed on pages that show attribute swatches.
 */

add_action('wp_footer', function () {
    // Only pages that show attribute swatches use the lightbox (Masterfold Core).
    if ( function_exists( 'mf_page_has_swatches' ) && ! mf_page_has_swatches() ) {
        return;
    }
    ?>
    <style>
		#copyFeedback {
  opacity: 0;
  transition: opacity 0.3s ease;
}

		#copyFeedback {
  animation: fadeInOut 2s ease forwards;
}

@keyframes fadeInOut {
  0% { opacity: 0; transform: translate(-50%, 5px); }
  10% { opacity: 1; transform: translate(-50%, 0); }
  90% { opacity: 1; transform: translate(-50%, 0); }
  100% { opacity: 0; transform: translate(-50%, 5px); }
}

		
.custom-lightbox-attribute-caption {
    user-select: text;
    cursor: text;
}
#customLightboxCopy svg {
  transition: transform 0.2s ease;
}
#customLightboxCopy:hover svg {
  transform: scale(1.15);
}
button#customLightboxCopy {
    margin-left: 0px ! Important;
    position: absolute;
    padding-top: 2px;
}
		
		
		

		div#customLightboxThumbnails {
    position: absolute;
    bottom: 0px;
}
		.custom-lightbox-attribute img {
    opacity: 1;
    transition-duration: 300ms;
    margin: 0px !important;
}

.custom-lightbox-attribute:hover img {
    opacity: 0.5;
}
.custom-lightbox-attribute {
    background-color: black;
    margin: 0.5px;
    border: 0px solid white;
}

		div#customLightboxThumbnails {
    -ms-overflow-style: none!important;  /* IE and Edge */
    scrollbar-width: none!important;     /* Firefox */
    overflow: auto!important;            /* ensure scrolling is still possible */
}

div#customLightboxThumbnails::-webkit-scrollbar {
    display: none!important;             /* Chrome, Safari and Opera */
}
		
		.custom-lightbox-attribute img {
    cursor: zoom-in !important;
}
		div#customLightboxThumbnails {
    max-width: fit-content !important;
}
		.custom-lightbox-attribute-header {
    background-color: black;
    max-width: fit-content;
    left: auto;
    padding: 10px;
    top: 0px;
    right: 0px;
}
        .custom-lightbox-attribute-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.85);
            z-index: 9999;
            display: none;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            color: white;
            padding: 20px;
        }
        .custom-lightbox-attribute-overlay.active {
            display: flex;
        }
        .custom-lightbox-attribute-header {
            position: absolute;
			background-color: black;
   			 max-width: fit-content;
       	     left: auto;
             padding: 10px;
             top: 0px;
            right: 0px;
            display: flex;
            justify-content: flex-end;
            gap: 0px;
            
            padding 10px;
        }
        .custom-lightbox-attribute-header button {
            background: none;
            border: none;
            color: white;
            font-size: 24px;
            cursor: pointer;
        }
        .custom-lightbox-attribute-header button img {
            max-width: 25px;
        }
        .custom-lightbox-attribute-main img {
            max-width: 90vw;
            max-height: 70vh;
            object-fit: contain;
            transition: transform 0.3s ease-in-out;
        }
        .custom-lightbox-attribute-caption {
            margin-top: 10px;
            font-size: 16px;
        }
        .custom-lightbox-attribute-thumbnails {
            display: flex;
            gap: 10px;
            margin-top: 20px;
            overflow-x: auto !important;
            overflow-y: hidden;
            -webkit-overflow-scrolling: touch;
            white-space: nowrap;
            padding-bottom: 10px;
            width: 75%;
            max-width: 75%!important;
        }
        div#customLightboxThumbnails {
            margin-top: 100px;
            scrollbar-color: #f39662 #00000000 !important;
            scrollbar-width: thin;
            padding: 20px 50px;
            min-height: 110px;
            overflow-y: visible !important;
        }
        .custom-lightbox-attribute-thumbnails img {
            height: 60px;
            width: 60px;
            object-fit: cover;
            cursor: pointer;
            border: 2px solid transparent;
            flex-shrink: 0;
        }
        .custom-lightbox-attribute-thumbnails img.active {
            border-color: white;
        }
        .custom-lightbox-attribute-nav {
            position: absolute;
            top: 45%;
            left: 0;
            right: 0;
            display: flex;
            justify-content: space-between;
            padding: 0 30px;
            pointer-events: none;
        }
        .custom-lightbox-attribute-nav button {
            background: none;
            border: none;
            color: white;
            font-size: 48px;
            cursor: pointer;
            pointer-events: all;
        }
    </style>

    <div class="custom-lightbox-attribute-overlay" id="customLightboxAttribute">
        <div class="custom-lightbox-attribute-header">
           
            <button id="customLightboxZoom" title="Zoom">
                <img 
                    src="/wp-content/uploads/zoom-plus-svgrepo-com.svg" 
                    alt="Zoom"
                >
            </button>
			 <button id="customLightboxClose" title="Close"><img style="max-width: 16px;" src="/wp-content/uploads/x-symbol-svgrepo-com.svg"></button>
        </div>

        <div class="custom-lightbox-attribute-main">
            <img id="customLightboxImage" src="" alt="">
        </div>

<div class="custom-lightbox-attribute-caption" id="customLightboxCaption">
  <span id="customLightboxCaptionText"></span>
<!--<button id="customLightboxCopy" title="Copy caption" style="background:none;border:none;cursor:pointer;margin-left:8px;display:inline-flex;align-items:center;position:relative;">-->
<button id="customLightboxCopy" title="Copy caption" style="background:none;border:none;cursor:pointer;margin-left:8px;display:none;align-items:center;position:relative;">
  <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" width="18" height="18">
    <path d="M15.24 2H11.3458C9.58159 1.99999 8.18418 1.99997 7.09054 2.1476C5.96501 2.29953 5.05402 2.61964 4.33559 3.34096C3.61717 4.06227 3.29833 4.97692 3.14701 6.10697C2.99997 7.205 2.99999 8.60802 3 10.3793V16.2169C3 17.725 3.91995 19.0174 5.22717 19.5592C5.15989 18.6498 5.15994 17.3737 5.16 16.312L5.16 11.3976L5.16 11.3024C5.15993 10.0207 5.15986 8.91644 5.27828 8.03211C5.40519 7.08438 5.69139 6.17592 6.4253 5.43906C7.15921 4.70219 8.06404 4.41485 9.00798 4.28743C9.88877 4.16854 10.9887 4.1686 12.2652 4.16867L12.36 4.16868H15.24L15.3348 4.16867C16.6113 4.1686 17.7088 4.16854 18.5896 4.28743C18.0627 2.94779 16.7616 2 15.24 2Z" fill="#ffffff"></path> 
    <path d="M6.6001 11.3974C6.6001 8.67119 6.6001 7.3081 7.44363 6.46118C8.28716 5.61426 9.64481 5.61426 12.3601 5.61426H15.2401C17.9554 5.61426 19.313 5.61426 20.1566 6.46118C21.0001 7.3081 21.0001 8.6712 21.0001 11.3974V16.2167C21.0001 18.9429 21.0001 20.306 20.1566 21.1529C19.313 21.9998 17.9554 21.9998 15.2401 21.9998H12.3601C9.64481 21.9998 8.28716 21.9998 7.44363 21.1529C6.6001 20.306 6.6001 18.9429 6.6001 16.2167V11.3974Z" fill="#ffffff"></path>
  </svg>
<span id="copyFeedback" style="display:none;position:absolute;bottom:120%;left:50%;transform:translateX(-50%);background:#000;color:#fff;font-size:12px;padding:3px 6px;border-radius:4px;pointer-events:none;">Copied!</span>

</button>

</div>


        <div class="custom-lightbox-attribute-thumbnails" id="customLightboxThumbnails"></div>

        <div class="custom-lightbox-attribute-nav">
            <button id="customLightboxPrev">&#10094;</button>
            <button id="customLightboxNext">&#10095;</button>
        </div>
    </div>

    <script>
   if (!window.customLightboxInitialized) {
        window.customLightboxInitialized = true;

        document.addEventListener("DOMContentLoaded", function () {
            const overlay = document.getElementById("customLightboxAttribute");
            const imgDisplay = document.getElementById("customLightboxImage");
            const caption = document.getElementById("customLightboxCaption");
            const thumbs = document.getElementById("customLightboxThumbnails");
            const closeBtn = document.getElementById("customLightboxClose");
            const zoomBtn = document.getElementById("customLightboxZoom");
            const zoomIcon = zoomBtn.querySelector("img");

            let allGroups = {};
            let currentGroup = null;
            let currentIndex = 0;
            let isZoomed = false;
			
			
			
			
			// Prevent overlay from closing when clicking inside content areas
document.querySelectorAll(
  ".custom-lightbox-attribute-main, .custom-lightbox-attribute-thumbnails, .custom-lightbox-attribute-nav, .custom-lightbox-attribute-header, .custom-lightbox-attribute-caption"
).forEach((el) => {
  el.addEventListener("click", (e) => {
    e.stopPropagation(); // Stop the click from bubbling up to overlay
  });
});

			
			

            document.querySelectorAll(".custom-lightbox-attribute").forEach(el => {
                const group = el.dataset.group;
                const image = el.dataset.image;
                const captionText = el.dataset.caption;
                const imgEl = el.querySelector("img");

                if (!allGroups[group]) allGroups[group] = [];
                allGroups[group].push({ el: imgEl, image, caption: captionText });

                if (!imgEl.dataset.listenerAdded) {
                    imgEl.addEventListener("click", () => {
                        openLightbox(group, image);
                    });
                    imgEl.dataset.listenerAdded = "true";
                }
            });

            function openLightbox(group, image) {
                overlay.classList.add("active");
                currentGroup = group;
                const groupImages = allGroups[group];
                currentIndex = groupImages.findIndex(img => img.image === image);
                if (currentIndex === -1) currentIndex = 0;

                updateLightbox(currentIndex);

                thumbs.innerHTML = "";
                const added = new Set();

                groupImages.forEach((item, index) => {
                    if (added.has(item.image)) return;
                    added.add(item.image);

                    const thumb = document.createElement("img");
                    thumb.src = item.image;
                    thumb.alt = item.caption;
                    thumb.classList.toggle("active", index === currentIndex);
                    thumb.addEventListener("click", () => {
                        currentIndex = index;
                        updateLightbox(currentIndex);
                    });
                    thumbs.appendChild(thumb);
                });
            }

                    function updateLightbox(index) {
            const imageObj = allGroups[currentGroup][index];
            imgDisplay.src = imageObj.image;
            document.getElementById("customLightboxCaptionText").textContent = imageObj.caption;


            // Loop through all thumbnails and update their borders
            Array.from(thumbs.children).forEach((thumb, i) => {
                const thumbImage = thumb.src.split('?')[0]; // Clean the URL to ignore query parameters
                const lightboxImage = imgDisplay.src.split('?')[0]; // Clean the URL for comparison

                // Add the 'active' class to the matching thumbnail
                if (lightboxImage === thumbImage) {
                    thumb.classList.add("active");

                    // Scroll the active thumbnail into view if it's out of the visible area
                    thumb.scrollIntoView({
                        behavior: 'smooth', // Smooth scroll
                        block: 'nearest',  // Scroll to the nearest edge (top/bottom)
                        inline: 'center'   // Center horizontally in the container
                    });
                } else {
                    thumb.classList.remove("active");
                }
            });
        }

        closeBtn.addEventListener("click", () => {
            overlay.classList.remove("active");
        });

        // Close on overlay click, but not inside lightbox content
overlay.addEventListener("click", (e) => {
    const isInsideContent =
        e.target.closest(".custom-lightbox-attribute-main") ||
        e.target.closest(".custom-lightbox-attribute-thumbnails") ||
        e.target.closest(".custom-lightbox-attribute-nav") ||
        e.target.closest(".custom-lightbox-attribute-header") ||
        e.target.closest(".custom-lightbox-attribute-caption");

    if (!isInsideContent) {
        overlay.classList.remove("active");
    }
});


            zoomBtn.addEventListener("click", (e) => {
                e.stopPropagation();
                isZoomed = !isZoomed;
                imgDisplay.style.transform = isZoomed ? "scale(1.2)" : "scale(1)";
                thumbs.style.display = isZoomed ? "none" : "flex";
                caption.style.display = isZoomed ? "none" : "block";
                zoomIcon.src = isZoomed
                    ? "/wp-content/uploads/zoom-minus-svgrepo-com.svg"
                    : "/wp-content/uploads/zoom-plus-svgrepo-com.svg";
            });

            closeBtn.addEventListener("click", () => {
                overlay.classList.remove("active");
                isZoomed = false;
                imgDisplay.style.transform = "scale(1)";
            });

            overlay.addEventListener("click", (e) => {
                if (
                    !e.target.closest(".custom-lightbox-attribute-main") &&
                    !e.target.closest(".custom-lightbox-attribute-thumbnails") &&
                    !e.target.closest(".custom-lightbox-attribute-nav")
                ) {
                    overlay.classList.remove("active");
                    isZoomed = false;
                    imgDisplay.style.transform = "scale(1)";
                }
            });

            document.getElementById("customLightboxPrev").addEventListener("click", (e) => {
                e.stopPropagation();
                const groupImages = allGroups[currentGroup];
                currentIndex = (currentIndex - 1 + groupImages.length) % groupImages.length;
                updateLightbox(currentIndex);
            });

            document.getElementById("customLightboxNext").addEventListener("click", (e) => {
                e.stopPropagation();
                const groupImages = allGroups[currentGroup];
                currentIndex = (currentIndex + 1) % groupImages.length;
                updateLightbox(currentIndex);
            });

            // Reset zoom on resize
            window.addEventListener("resize", () => {
                if (isZoomed) {
                    imgDisplay.style.transform = "scale(1.2)";
                } else {
                    imgDisplay.style.transform = "scale(1)";
                }
            });
			
			
const copyBtn = document.getElementById("customLightboxCopy");
const copyFeedback = document.getElementById("copyFeedback");

copyBtn.addEventListener("click", (e) => {
    e.stopPropagation();
    const text = document.getElementById("customLightboxCaptionText").textContent.trim();
    if (!text) return;

    navigator.clipboard.writeText(text).then(() => {
        // Show the feedback
        copyFeedback.style.display = "block";
        copyFeedback.style.opacity = "1";

        // Hide it after 2 seconds
        setTimeout(() => {
            copyFeedback.style.opacity = "0";
            setTimeout(() => copyFeedback.style.display = "none", 300); // hide completely after fade
        }, 2000);
    });
});




			
			
        });
    }







    </script>
    <?php
});

