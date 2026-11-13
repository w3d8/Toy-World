// تحسينات على دالة addToCart
function addToCart(category, index) {
  const product = products[category][index];
  
  // التحقق مما إذا كان المنتج موجودًا بالفعل في السلة
  const existingItem = cart.find(item => item.name === product.name);
  
  if (existingItem) {
    existingItem.quantity = (existingItem.quantity || 1) + 1;
  } else {
    product.quantity = 1;
    cart.push(product);
  }
  
  // إعادة حساب السعر الإجمالي
  totalPrice = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
  
  // حفظ في localStorage
  localStorage.setItem('cart', JSON.stringify(cart));
  localStorage.setItem('totalPrice', totalPrice);
  
  updateCart();
  
  // إظهار إشعار مؤقت
  showNotification(`${product.name} تمت إضافته إلى السلة`);
}

// دالة جديدة لإظهار الإشعارات
function showNotification(message) {
  const notification = document.createElement('div');
  notification.className = 'notification';
  notification.textContent = message;
  document.body.appendChild(notification);
  
  setTimeout(() => {
    notification.classList.add('fade-out');
    setTimeout(() => notification.remove(), 500);
  }, 2000);
}

// تحسين دالة updateCart
function updateCart() {
  const cartItems = document.getElementById("cart-items");
  cartItems.innerHTML = "";
  
  if (cart.length === 0) {
    cartItems.innerHTML = "<li>السلة فارغة</li>";
    document.getElementById("total-price").textContent = "0";
  } else {
    cart.forEach((item, index) => {
      const li = document.createElement("li");
      li.className = "cart-item";
      li.innerHTML = `
        <div class="cart-item-info">
          <span>${item.name}</span>
          <span>${item.price} SAR × ${item.quantity}</span>
        </div>
        <div class="cart-item-actions">
          <button onclick="changeQuantity(${index}, -1)">-</button>
          <span>${item.quantity}</span>
          <button onclick="changeQuantity(${index}, 1)">+</button>
          <button onclick="removeFromCart(${index})">🗑️</button>
        </div>
      `;
      cartItems.appendChild(li);
    });
    
    document.getElementById("total-price").textContent = totalPrice;
  }
  
  document.getElementById("cart-count").textContent = cart.reduce((sum, item) => sum + item.quantity, 0);
}

// دوال جديدة لإدارة الكميات
function changeQuantity(index, change) {
  const item = cart[index];
  item.quantity += change;
  
  if (item.quantity <= 0) {
    cart.splice(index, 1);
  }
  
  totalPrice = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
  
  localStorage.setItem('cart', JSON.stringify(cart));
  localStorage.setItem('totalPrice', totalPrice);
  updateCart();
}

function removeFromCart(index) {
  cart.splice(index, 1);
  totalPrice = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
  
  localStorage.setItem('cart', JSON.stringify(cart));
  localStorage.setItem('totalPrice', totalPrice);
  updateCart();
}

// دالة للدفع
function checkout() {
  if (cart.length === 0) {
    showNotification("السلة فارغة، أضف منتجات أولاً");
    return;
  }
  
  if (confirm(`هل تريد إتمام الشراء بقيمة ${totalPrice} SAR؟`)) {
    cart = [];
    totalPrice = 0;
    localStorage.removeItem('cart');
    localStorage.removeItem('totalPrice');
    updateCart();
    showNotification("تم إتمام الطلب بنجاح!");
  }
}

// إضافة زر الدفع إلى السلة
document.addEventListener('DOMContentLoaded', function() {
  const cartSidebar = document.getElementById('cart-sidebar');
  const checkoutBtn = document.createElement('button');
  checkoutBtn.className = 'btn btn-success w-100 mt-3';
  checkoutBtn.textContent = 'إتمام الشراء';
  checkoutBtn.onclick = checkout;
  cartSidebar.appendChild(checkoutBtn);
});