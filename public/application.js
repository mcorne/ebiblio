function accordion(id)
{
    var element = document.getElementById(id);

    if (element.className.indexOf("w3-show") == -1) {
        element.className += " w3-show";
        element.previousElementSibling.className += " w3-green";
    } else {
        element.className = element.className.replace(" w3-show", "");
        element.previousElementSibling.className =
        element.previousElementSibling.className.replace(" w3-green", "");
    }
}

function close_sidebar()
{
    document.getElementById("overlay").style.display = "none";
    document.getElementById("sidebar").style.display = "none";
}

function open_sidebar()
{
    document.getElementById("overlay").style.display = "block";
    document.getElementById("sidebar").style.display = "block";
}
