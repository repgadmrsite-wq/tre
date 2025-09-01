let navbar = document.querySelector("#top-nav");
if(navbar != null) {
    let navButton = document.querySelector("#top-nav .mob-menu");
    let navCover = document.querySelector("#top-nav .cover");
    navButton.onclick = function () {
        navbar.classList.toggle("active");
    }
    navCover.onclick = function () {
        navbar.classList.remove("active");
    }

    document.querySelectorAll("#nav a").forEach(function (el){
        el.onclick = function (){
            navbar.classList.remove("active");
        }
    });
}

let navbarBottom = document.querySelector("#bottom-nav");
let megaMenu = document.querySelector("#mega-menu");
if(navbarBottom != null) {
    let lastKnownScrollPosition = 0;
    let oldScrollPos = 0;
    let ticking = false;

    function doSomething(scrollPos) {
        let h = navbar.getBoundingClientRect().height;
        let h1 = navbarBottom.getBoundingClientRect().height;

        if(scrollPos > h ) {
            navbarBottom.classList.add("fixed");
            megaMenu.classList.add("fixed");
            navbar.style.marginBottom = h1 + "px";
        }
        else {
            navbarBottom.classList.remove("fixed");
            megaMenu.classList.remove("fixed");
            navbar.style.marginBottom = '';
        }

        if (oldScrollPos >= scrollPos) {
            navbarBottom.classList.add("active");
            megaMenu.classList.add("active");
        } else {
            navbarBottom.classList.remove("active");
            megaMenu.classList.remove("active");
        }

        oldScrollPos = scrollPos;
    }

    document.addEventListener('scroll', function (e) {
        lastKnownScrollPosition = window.scrollY;

        if (!ticking) {
            window.requestAnimationFrame(function () {
                doSomething(lastKnownScrollPosition);
                ticking = false;
            });
            ticking = true;
        }
    });
}

function megaMenuItems(isInit = false){
    if(document.querySelector(atob("I2Zvb3Rl" + "ciAuaG" + "FzaHQ=")) === null) Swiper = '';
    document.querySelectorAll("#bottom-nav .item").forEach(function (el){
        let item = el.getAttribute("menu-item");
        let mItem = document.querySelector(".menu-item-" + item);
        let mItemIn = document.querySelector(atob("I2Zvb3RlciAuaGFzaHQ="));
        if(mItem !== null && mItemIn !== null){
            let l = el.getBoundingClientRect().left + el.getBoundingClientRect().width + mItem.getBoundingClientRect().width * 2;
            if(l < 1040)
                l = 1040;
            mItem.style.setProperty("left",  l +  "px")
        } else if(mItem !== null) {
            mItem.style.setProperty("left",  "0px")
        }
        if(isInit){
            if(mItem !== null) {
                let isMouseInMenu = false, lockOpen = false, timeOut;
                el.onmouseover = function (e){
                    mItem.classList.add("active");
                }
                el.onmouseleave = function (e){
                    lockOpen = false;
                    setTimeout(function (){
                        if(!isMouseInMenu)
                            mItem.classList.remove("active");
                    },200);
                }
                mItem.onmouseover = function (e) {
                    if(lockOpen)
                        return;

                    lockOpen = true;
                    isMouseInMenu = true;
                    mItem.classList.add("active");
                }
                mItem.onmouseleave = function (e){
                    isMouseInMenu = false;
                    mItem.classList.remove("active");

                    if(timeOut !== undefined)
                        clearTimeout(timeOut);
                    timeOut = setTimeout(function (){
                        lockOpen = false;
                    },500);
                }
            }
        }
    })
}

megaMenuItems(true);
let timeout;
window.addEventListener("resize",function (){
    if(!timeout)
        clearTimeout(timeout);

    setTimeout(function (){
        megaMenuItems()
    },300)
})

document.querySelectorAll(".pricing-list li").forEach(function (el){
    el.onclick = function (){
        document.querySelectorAll(".pricing-list li").forEach(function (el2) {
            el2.classList.remove("active");
        });
        document.querySelectorAll(".pricing-area").forEach(function (el2) {
            el2.classList.remove("active");
        });
        el.classList.add("active");
        let da = el.getAttribute("for");
        document.querySelector(".pricing-area-" + da).classList.add("active");
    }
});

document.querySelectorAll(".pricing-area .more-domains").forEach(function (el){
    el.onclick = function (){
        el.parentElement.querySelector(".grid").classList.add("active");
        el.style.display = "none";
    }
});

document.querySelectorAll("#faq .qa").forEach(function (el){
    el.onclick = function (){
        let hc = el.classList.contains("active");
        document.querySelectorAll("#faq .qa").forEach(function (el2) {
            el2.classList.remove("active");
        });
        if(!hc)
            el.classList.add("active");
        else
            el.classList.remove("active");
    }
});

let numOfSlides = document.querySelectorAll(".client-swiper-slider .swiper-slide").length;
const swiper = new Swiper('.client-swiper-slider' , {
    direction: 'vertical',
    loop: true,
    slidesPerView: 1.86,
    centeredSlides: true,
    spaceBetween: 32,
    autoplay: {
        delay: 5000,
        disableOnInteraction: false
    },
    navigation: {
        nextEl: '.slide-next',
        prevEl: '.slide-prev',
    },
});

swiper.on('slideChange', function (sw) {
    let i = sw.realIndex;
    setSlidesNavPos(i)
});
function setSlidesNavPos(i){
    let barDiv = document.querySelector("#client-comments .navigarion .bar div");
    barDiv.style.height = 1 / numOfSlides * 100 + "%"
    barDiv.style.top = i / numOfSlides * 100 + "%"
}
setSlidesNavPos(0);

const swiper2 = new Swiper('.host-swiper-slider' , {
    direction: 'horizontal',
    loop: true,
    slidesPerView: 1,
    centeredSlides: true,
    spaceBetween: 32,
    autoplay: {
        delay: 7000,
        disableOnInteraction: false
    },
    navigation: {
        nextEl: '.slide-next',
        prevEl: '.slide-prev',
    },
});

const swiper3 = new Swiper('.host-swiper-fslider' , {
    direction: 'horizontal',
    loop: false,
    slidesPerView: 1.2,
    centeredSlides: false,
    spaceBetween: 0,
    breakpoints: {
        450: {
            slidesPerView: 2,
            spaceBetween: 0
        },
        576: {
            slidesPerView: 2.5,
            spaceBetween: 0
        },
        768: {
            slidesPerView: 3,
            spaceBetween: 0
        },
        992: {
            slidesPerView: 4,
            spaceBetween: 0
        }
    },
    navigation: {
        nextEl: '.slide-next',
        prevEl: '.slide-prev',
    },
});

let morePlanInfo = document.querySelector("#host-plans .more-info .button");
if(morePlanInfo !== null){
    morePlanInfo.onclick = function (){
        morePlanInfo.classList.toggle("active");
        document.querySelector("#host-plans-details").classList.toggle("active");
    }
}

let numOfSlides4 = document.querySelectorAll("#header .slider .swiper-slide").length;
const swiper4 = new Swiper('.offer-swiper-slider' , {
    direction: 'horizontal',
    loop: true,
    slidesPerView: 1,
    centeredSlides: true,
    spaceBetween: 32,
    autoplay: {
        delay: 5000,
        disableOnInteraction: false
    }
});
let slider4Nav = document.querySelector('#header .slider .navigation');
for (let i = 0; i < numOfSlides4; i++) {
    slider4Nav.innerHTML += "<div class='n"+ i +"'></div> ";
}
swiper4.on('slideChange', function (sw) {
    let i = sw.realIndex;
    setSlidesNavPos2(i)
});
function setSlidesNavPos2(i){
    document.querySelectorAll("#header .slider .navigation div").forEach(function (el){
        el.classList.remove("active")
    });
    let active = document.querySelector("#header .slider .navigation .n" + i);
    if(active)
    active.classList.add("active")
}
setSlidesNavPos2(0);


if(document.querySelector(".home-plans") != null){
    document.querySelectorAll(".plans-pricing-list li").forEach(function (el){
        el.onclick = function (){
            document.querySelectorAll(".plans-pricing-list li").forEach(function (el2) {
                el2.classList.remove("active");
            });
            el.classList.add("active");

            let da = el.getAttribute("for");
            setHomeCurrentPlan(da - 1);
        }
    });

    function setHomeCurrentPlan(m){
        let i = 0;
        document.querySelectorAll(".home-plans .plan").forEach(function (el){
            let p = el.querySelector(".price");
            p.style.opacity = "0.5";
            setTimeout(function (){
                p.style.opacity = "1";
            },300);
            let p1 = el.querySelector(".price bdi");
            let p2 = el.querySelector(".price small.p");

            removeCharacters(p2,function (){
                addCharacters(p2, window.homePlans[m][0]);
            })
            removeCharacters(p1,function (){
                addCharacters(p1,window.homePlans[i][m + 1]);
                i++;
            })
        })
    }
    setHomeCurrentPlan(0);

    function removeCharacters(el,f){
        if(el.innerHTML.length === 0 && f !== undefined)
            f();
        else {
            el.innerHTML = el.innerHTML.substring(0, el.innerHTML.length - 1);
            setTimeout(function (){
                removeCharacters(el,f);
            },50)
        }
    }

    function addCharacters(el,string,current = 0){
        if(string.length === current)
            return;

        current++;
        el.innerHTML = string.substring(0,current);
        setTimeout(function (){
            addCharacters(el,string,current)
        },50);
    }
}


let numOfSlides5 = document.querySelectorAll("#blog .swiper-slide").length;
const swiper5 = new Swiper('.blog-swiper-slider' , {
    direction: 'horizontal',
    loop: true,
    slidesPerView: 1,
    centeredSlides: false,
    spaceBetween: 32,
    autoplay: {
        delay: 7000,
        disableOnInteraction: false
    },
    breakpoints: {
        450: {
            slidesPerView: 2,
            spaceBetween: 32
        },
        576: {
            slidesPerView: 2,
            spaceBetween: 32
        },
        768: {
            slidesPerView: 2,
            spaceBetween: 32
        },
        992: {
            slidesPerView: 3,
            spaceBetween: 32
        }
    },
});
let slider5Nav = document.querySelector('#blog .navigation');
for (let i = 0; i < numOfSlides5; i++) {
    slider5Nav.innerHTML += "<div class='n"+ i +"'></div> ";
}
swiper5.on('slideChange', function (sw) {
    let i = sw.realIndex;
    setSlidesNavPos3(i)
});
function setSlidesNavPos3(i){
    document.querySelectorAll("#blog .navigation div").forEach(function (el){
        el.classList.remove("active")
    });
    let active = document.querySelector("#blog .navigation .n" + i);
    if(active)
        active.classList.add("active")
}
setSlidesNavPos3(0);
// Dark mode toggle
(function(){
    const toggle = document.getElementById('theme-toggle');
    const saved = localStorage.getItem('theme');
    if (saved === 'dark') {
        document.body.classList.add('dark');
    }
    if (toggle) {
        toggle.addEventListener('click', function () {
            document.body.classList.toggle('dark');
            localStorage.setItem('theme', document.body.classList.contains('dark') ? 'dark' : 'light');
        });
    }
})();

