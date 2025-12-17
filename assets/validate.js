
function validateForm() {
    let name=document.getElementById("full_name").value.trim();
    let email=document.getElementById("email").value.trim();
    let event=document.getElementById("event_id").value.trim();

    if(name === ""){ alert("Name required"); return false; }

    let emailPattern=/^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if(!emailPattern.test(email)){ alert("Invalid email"); return false;}

    if(event === ""){ alert("Select event"); return false;}

    return true;
}
