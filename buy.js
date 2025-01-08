// let buy = document.getElementsByTagName('button');
// buy.addEventlistener('click',function(){
// 	alert('sp đã đc thanh toán');
// }
// )

var order = document.getElementById("order");

function buy() {
	var name = document.getElementById("name").value;
	var email = document.getElementById("email").value;
	var address = document.getElementById("address").value;
	var quantity = document.getElementById("quantity").value;

	var container = document.getElementsByClassName("container")[0];
	container.innerHTML = `<h1 style="text-align: center;">ĐẶT HÀNG HOÀN TẤT</h1>`;
	container.innerHTML += `<h2 style="text-align: left; padding-left: 25%;">tên: ${name}</h2>`;
	container.innerHTML += `<h2 style="text-align: left; padding-left: 25%;">email: ${email}</h2>`; 
	container.innerHTML += `<h2 style="text-align: left; padding-left: 25%;">địa chỉ: ${address}</h2>`; 
	container.innerHTML += `<h2 style="text-align: left; padding-left: 25%;">số lượng: ${quantity}</h2>`; 
}

order.addEventListener("click", buy);
