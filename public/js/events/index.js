// View More button interaction
document.querySelector(".view-more-btn").addEventListener("click", function () {
    alert("Loading more events...");
    this.textContent = "Loading...";
    this.disabled = true;

    // Simulate loading
    setTimeout(() => {
        this.textContent = "View More Events";
        this.disabled = false;
    }, 1500);
});
