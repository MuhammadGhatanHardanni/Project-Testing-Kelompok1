/* DailyMart v3.0 — app.js */
'use strict';

// ── Theme ──────────────────────────────────────────────────────────
const Theme = {
  key: 'dm_theme',
  get(){ return localStorage.getItem(this.key)||'light'; },
  set(t){
    localStorage.setItem(this.key,t);
    document.documentElement.setAttribute('data-theme',t);
    document.cookie=`dm_theme=${t};path=/;max-age=31536000`;
    this.syncIcons(t);
  },
  toggle(){ this.set(this.get()==='dark'?'light':'dark'); },
  syncIcons(t){
    document.querySelectorAll('.theme-icon-sun').forEach(e=>e.classList.toggle('d-none',t==='light'));
    document.querySelectorAll('.theme-icon-moon').forEach(e=>e.classList.toggle('d-none',t==='dark'));
  },
  init(){
    const t=this.get();
    document.documentElement.setAttribute('data-theme',t);
    this.syncIcons(t);
    document.querySelectorAll('.theme-toggle').forEach(b=>b.addEventListener('click',()=>this.toggle()));
  }
};
Theme.init();

// ── Navbar scroll ─────────────────────────────────────────────────
window.addEventListener('scroll',()=>{
  document.getElementById('mainNav')?.classList.toggle('scrolled',window.scrollY>60);
},{passive:true});

// ── Auto-dismiss alerts ───────────────────────────────────────────
document.querySelectorAll('.alert').forEach(a=>{
  setTimeout(()=>{if(a.classList.contains('show')){a.classList.remove('show');setTimeout(()=>a.remove(),300);}},4500);
});

// ── Wishlist toggle ───────────────────────────────────────────────
document.querySelectorAll('.wish-btn').forEach(btn=>{
  btn.addEventListener('click',async function(e){
    e.preventDefault();
    const pid=this.dataset.pid;
    if(!pid){window.location=BASE_URL+'/auth/login';return;}
    this.style.transform='scale(1.35)';
    setTimeout(()=>this.style.transform='',250);
    try{
      const res=await fetch(BASE_URL+'/wishlist/toggle',{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:`product_id=${pid}&csrf_token=${CSRF_TOKEN}`});
      const d=await res.json();
      const isAdded=d.action==='added';
      this.classList.toggle('wishlisted',isAdded);
      this.querySelector('i').className=isAdded?'bi bi-heart-fill':'bi bi-heart';
      document.querySelectorAll('.wishlist-count-badge').forEach(el=>{
        el.textContent=d.count||'';
        el.style.display=d.count>0?'':'none';
      });
      toast(isAdded?'Ditambahkan ke wishlist ❤️':'Dihapus dari wishlist', isAdded?'success':'info');
    }catch(err){console.error(err);}
  });
});

// ── Add to Cart (AJAX) ────────────────────────────────────────────
document.querySelectorAll('.add-to-cart-form').forEach(form=>{
  form.addEventListener('submit',async function(e){
    e.preventDefault();
    const btn=this.querySelector('.add-btn');
    const orig=btn.innerHTML;
    btn.disabled=true;
    btn.innerHTML='<span class="spinner-border spinner-border-sm"></span>';
    try{
      const fd=new FormData(this);
      const res=await fetch(BASE_URL+'/cart/add',{method:'POST',body:fd});
      const d=await res.json();
      if(d.success){
        btn.innerHTML='<i class="bi bi-check-lg me-1"></i>Ditambahkan!';
        btn.classList.replace('btn-primary','btn-success');
        document.querySelectorAll('.cart-count-badge').forEach(el=>{el.textContent=d.count;el.style.display=d.count>0?'':'none';});
        toast(d.message||'Produk ditambahkan ke keranjang','success');
        setTimeout(()=>{btn.innerHTML=orig;btn.classList.replace('btn-success','btn-primary');btn.disabled=false;},2000);
      }
    }catch(err){btn.innerHTML=orig;btn.disabled=false;}
  });
});

// ── Qty controls ──────────────────────────────────────────────────
function initQty(){
  document.querySelectorAll('.qty-wrap').forEach(wrap=>{
    const inp=wrap.querySelector('.qty-in');
    if(!inp)return;
    wrap.querySelector('[data-delta="-1"]')?.addEventListener('click',()=>{
      const v=Math.max(1,parseInt(inp.value)-1);inp.value=v;inp.dispatchEvent(new Event('change'));
    });
    wrap.querySelector('[data-delta="1"]')?.addEventListener('click',()=>{
      const max=parseInt(inp.max)||9999;
      const v=Math.min(max,parseInt(inp.value)+1);inp.value=v;inp.dispatchEvent(new Event('change'));
    });
    inp.addEventListener('change',()=>{
      const max=parseInt(inp.max)||9999;
      let v=parseInt(inp.value)||1;
      inp.value=Math.max(1,Math.min(v,max));
    });
  });
}
initQty();

// ── Star rating input ─────────────────────────────────────────────
const starBtns=document.querySelectorAll('.star-input');
const ratingVal=document.getElementById('ratingValue');
starBtns.forEach(btn=>{
  btn.addEventListener('click',function(){
    const v=parseInt(this.dataset.val);
    if(ratingVal)ratingVal.value=v;
    starBtns.forEach((s,i)=>{s.querySelector('i').className=i<v?'bi bi-star-fill':'bi bi-star';});
  });
  btn.addEventListener('mouseenter',function(){
    const v=parseInt(this.dataset.val);
    starBtns.forEach((s,i)=>{s.querySelector('i').className=i<v?'bi bi-star-fill':'bi bi-star';});
  });
});
document.querySelector('.star-rating-input')?.addEventListener('mouseleave',()=>{
  const cur=parseInt(ratingVal?.value||0);
  starBtns.forEach((s,i)=>{s.querySelector('i').className=i<cur?'bi bi-star-fill':'bi bi-star';});
});

// ── Voucher AJAX ──────────────────────────────────────────────────
const vForm=document.getElementById('voucherForm');
if(vForm){
  vForm.addEventListener('submit',async function(e){
    e.preventDefault();
    const code=document.getElementById('voucherCode').value.trim().toUpperCase();
    const sub=parseFloat(document.getElementById('cartSubtotal')?.value||0);
    const msgEl=document.getElementById('voucherMsg');
    const btn=this.querySelector('button[type="submit"]');
    const orig=btn.innerHTML;
    btn.disabled=true;btn.innerHTML='<span class="spinner-border spinner-border-sm"></span>';
    try{
      const res=await fetch(BASE_URL+'/checkout/apply-voucher',{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:`code=${encodeURIComponent(code)}&subtotal=${sub}&csrf_token=${CSRF_TOKEN}`});
      const d=await res.json();
      msgEl.className='mt-2 small fw-600 '+(d.success?'text-success':'text-danger');
      msgEl.textContent=d.message;
      if(d.success){
        document.getElementById('discountRow')?.classList.remove('d-none');
        document.getElementById('discountAmt').textContent=d.discount_fmt;
        document.getElementById('totalDisplay').textContent=d.total_fmt;
        document.getElementById('appliedVoucherCode').value=code;
        document.getElementById('removeVoucherBtn')?.classList.remove('d-none');
        this.querySelector('button[type="submit"]').classList.add('d-none');
      }
    }catch(err){msgEl.textContent='Gagal menghubungi server.';msgEl.className='mt-2 small text-danger';}
    finally{btn.disabled=false;btn.innerHTML=orig;}
  });
}
document.getElementById('removeVoucherBtn')?.addEventListener('click',async function(){
  await fetch(BASE_URL+'/checkout/remove-voucher',{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:`csrf_token=${CSRF_TOKEN}`});
  document.getElementById('discountRow')?.classList.add('d-none');
  document.getElementById('voucherMsg').textContent='';
  document.getElementById('appliedVoucherCode').value='';
  const sub=parseFloat(document.getElementById('cartSubtotal')?.value||0);
  document.getElementById('totalDisplay').textContent='Rp '+sub.toLocaleString('id-ID');
  this.classList.add('d-none');
  document.getElementById('voucherCode').value='';
  document.querySelector('#voucherForm button[type="submit"]')?.classList.remove('d-none');
});

// ── Toast ─────────────────────────────────────────────────────────
function toast(msg,type='success'){
  const bg={success:'#00b96b',error:'#ef4444',info:'#3b82f6',warning:'#f59e0b'};
  const el=document.createElement('div');
  el.style.cssText=`position:fixed;bottom:1.5rem;right:1.5rem;z-index:9999;background:${bg[type]||bg.success};color:#fff;padding:.75rem 1.25rem;border-radius:12px;font-weight:600;font-size:.88rem;box-shadow:0 8px 30px rgba(0,0,0,.2);animation:fadeUp .3s ease;max-width:300px;font-family:'Outfit',sans-serif`;
  el.textContent=msg;
  document.body.appendChild(el);
  setTimeout(()=>{el.style.opacity='0';el.style.transform='translateY(8px)';el.style.transition='all .3s';setTimeout(()=>el.remove(),300);},3200);
}
window.toast=toast;

// ── Copy voucher code ─────────────────────────────────────────────
document.querySelectorAll('[data-copy]').forEach(el=>{
  el.addEventListener('click',function(){
    navigator.clipboard.writeText(this.dataset.copy||this.textContent.trim());
    const orig=this.textContent;
    this.textContent='✓ Disalin!';
    setTimeout(()=>this.textContent=orig,1800);
  });
});

// ── Lazy img fade ─────────────────────────────────────────────────
document.querySelectorAll('img[loading="lazy"]').forEach(img=>{
  img.style.opacity='0';img.style.transition='opacity .35s ease';
  img.addEventListener('load',()=>img.style.opacity='1');
  if(img.complete)img.style.opacity='1';
});

// ── Address selector checkout ─────────────────────────────────────
document.querySelectorAll('.addr-option input[type=radio]').forEach(r=>{
  r.addEventListener('change',function(){
    const d=JSON.parse(this.closest('[data-addr]')?.dataset.addr||'{}');
    if(d.recipient) document.getElementById('recipientName').value=d.recipient;
    if(d.phone)     document.getElementById('phoneField').value=d.phone;
    if(d.address)   document.getElementById('addressField').value=d.address;
    if(d.city)      document.getElementById('cityField').value=d.city;
  });
});

// ── Admin sidebar ─────────────────────────────────────────────────
document.getElementById('sidebarToggle')?.addEventListener('click',()=>{
  document.querySelector('.adm-sidebar')?.classList.toggle('open');
});
document.addEventListener('click',e=>{
  const sb=document.querySelector('.adm-sidebar'),tb=document.getElementById('sidebarToggle');
  if(sb?.classList.contains('open')&&!sb.contains(e.target)&&!tb?.contains(e.target))sb.classList.remove('open');
});

// ── Confirm dialogs ───────────────────────────────────────────────
document.querySelectorAll('[data-confirm]').forEach(el=>{
  el.addEventListener('click',e=>{if(!confirm(el.dataset.confirm||'Yakin?'))e.preventDefault();});
});

// ── Image error fallback ──────────────────────────────────────────
document.querySelectorAll('img').forEach(img=>{
  if(!img.hasAttribute('onerror')){
    img.addEventListener('error',function(){
      this.onerror=null;
      this.src='https://images.unsplash.com/photo-1542838132-92c53300491e?w=600&q=80';
    });
  }
});
