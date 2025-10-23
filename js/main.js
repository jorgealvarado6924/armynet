

let upboton=document.getElementById("subirboton");
upboton.addEventListener("click", function(){
    document.documentElement.scrollTop=0;   
})


window.addEventListener("scroll", function(){
    if (document.documentElement.scrollTop>0) {
        upboton.style.display="flex";
    } else {
        upboton.style.display="none";
    }
});



