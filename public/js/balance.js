//custom balance
document.querySelector("#startDate").valueAsDate = new Date();
document.querySelector("#endDate").valueAsDate = new Date();
const okButton = document.getElementById("okButton");

okButton.addEventListener("click", function() {

  const startDate = document.getElementById("startDate").value;
  const endDate = document.getElementById("endDate").value;

  loadCustomBalance(startDate,endDate);

});

const loadCustomBalance = (startDate, endDate) => {
  const currentURL = window.location.href;
  const hasCustomBalanceSegment = currentURL.includes('/custombalance/');

  let newPageURL;

  if (hasCustomBalanceSegment) {
    newPageURL = currentURL.replace(/\/custombalance\/.*$/, `/custombalance/${startDate}/${endDate}`);
  } else {
    newPageURL = `/custombalance/${startDate}/${endDate}`;
  }

  window.location.href = newPageURL;
};







