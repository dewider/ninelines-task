document.addEventListener("DOMContentLoaded", function(){
    let tabs = document.querySelectorAll('button.tab');
    tabs.forEach(item => item.addEventListener('click', function(e){
        e.preventDefault();
        let year = e.currentTarget.dataset.year;
        if(!year || year < 1970 || year > 3000) return;
        let currentUrl = new URL(location.href);
        currentUrl.searchParams.set('year', year);
        currentUrl.searchParams.delete('nav-news', year);
        location.href = currentUrl.toString();
    }));
});