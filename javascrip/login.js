/*const btn = document.getElementById("btn");
const container = document.querySelector(".container");

btn.addEventListener("click", () => {
    container.classList.toggle("toggle");
});*/

const container = document.querySelector(".container");
const btnSingIn = document.getElementById("btn-sign-in");
const btnSingUp = document.getElementById("btn-sign-up");

btnSingIn.addEventListener("click", () => {
    container.classList.remove("toggle");
});
btnSingUp.addEventListener("click", () => {
    container.classList.add("toggle");
});