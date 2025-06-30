    let continentSelected = document.querySelector("#continent_list");
    let regionSelected = document.querySelector("#region_list");
    let text_region = document.querySelector("#text_region");
    let text_continent = document.querySelector("#text_continent");

    document.querySelector('#title_continent').innerHTML =  '<span>' + continentSelected.options[continentSelected.selectedIndex].text + '</span>' + ' - Estimations 2025';
    
    let number = parseInt(continentSelected.value);

    if(number === 3 || number === 7){
        regionSelected.style.display = 'none';
        text_region.style= 'visibility: hidden';
        text_continent.style.display = 'block';

    } else {
         regionSelected.style.display = 'inline-blocke';
         text_region.style.display = 'inline-blocke';
         text_continent.style.display = 'inline-blocke';
    }

document.querySelector('#continent_list').addEventListener('change', function(e){
        regionSelected.value="";
        document.querySelector('form').submit();
});
document.querySelector('#region_list').addEventListener('change', function(){
        document.querySelector('form').submit();

});