// add hovered class to selecyed list item
let list = document.querySelectorAll(".navigation li");

function activeLink(){
    list.forEach(item=>{
        item.classList.remove("hovered");
    })
    this.classList.add("hovered");
}

list.forEach((item) => item.addEventListener("mouseover", activeLink));


//modal sa may verification
function openModal(id, name, email, date) {
    document.getElementById("modalStudentID").textContent = id;
    document.getElementById("modalStudentName").textContent = name;
    document.getElementById("modalStudentEmail").textContent = email;
    document.getElementById("modalStudentDate").textContent = date;
    document.getElementById("studentModal").style.display = "flex";
}

function closeModal() {
    document.getElementById("studentModal").style.display = "none";
}