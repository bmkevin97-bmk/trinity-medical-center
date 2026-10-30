/* Evita que guardar, eliminar o volver llenen el historial del navegador. */
(function(){

  if(window.__sinHistorial){
    document.removeEventListener('submit', window.__sinHistorial.submit);
    document.removeEventListener('click',  window.__sinHistorial.click);
  }

  // Formularios POST: se envían con fetch y la página se refresca sin crear una entrada nueva
  function submit(e){
    var f = e.target;
    if(!f.method || f.method.toLowerCase() !== 'post' || e.defaultPrevented) return;
    e.preventDefault();

    var btn   = e.submitter || null;
    var datos = new FormData(f, btn);
    if(btn) btn.disabled = true;

    fetch(f.action || location.href, {method:'POST', body:datos, credentials:'same-origin'})
      .then(function(r){
        if(r.redirected){
          if(r.url.split('#')[0] === location.href.split('#')[0]) location.reload();
          else location.replace(r.url);
          return;
        }
        return r.text().then(function(html){
          document.open(); document.write(html); document.close();
        });
      })
      .catch(function(){ if(btn) btn.disabled = false; f.submit(); });
  }

  // Enlaces: eliminar y cerrar sesión reemplazan la entrada; "volver" usa el historial
  function click(e){
    if(e.defaultPrevented) return;
    var a = e.target.closest && e.target.closest('a');
    if(!a) return;

    if(a.classList.contains('btn-eliminar') || a.classList.contains('btn-logout')){
      e.preventDefault();
      location.replace(a.href);
      return;
    }

    if(a.classList.contains('btn-volver') && document.referrer && history.length > 1){
      try{
        if(new URL(document.referrer).pathname === new URL(a.href).pathname){
          e.preventDefault();
          history.back();
        }
      }catch(_){}
    }
  }

  document.addEventListener('submit', submit);
  document.addEventListener('click', click);
  window.__sinHistorial = {submit: submit, click: click};
})();

/* ===== Mejoras de interfaz en páginas de gestión ===== */
(function(){
  function init(){
    // 1) Campos del formulario: agrupa cada <label> con su control
    document.querySelectorAll('.form-card form').forEach(function(f){
      Array.from(f.querySelectorAll(':scope > label')).forEach(function(l){
        var c = l.nextElementSibling;
        if(!c || c.tagName === 'LABEL' || c.tagName === 'BUTTON') return;
        var d = document.createElement('div'); d.className = 'campo';
        l.parentNode.insertBefore(d, l); d.appendChild(l); d.appendChild(c);
      });
    });

    // 2) Tablas: scroll, contador, buscador y textos largos plegables
    document.querySelectorAll('.table-card').forEach(function(card){
      var t = card.querySelector('table'); if(!t) return;

      if(!t.parentElement.classList.contains('table-scroll')){
        var w = document.createElement('div'); w.className = 'table-scroll';
        t.parentNode.insertBefore(w, t); w.appendChild(t);
      }

      var filas = Array.from(t.querySelectorAll('tr')).filter(function(r){ return !r.querySelector('th'); });
      var h2 = card.querySelector('h2');
      if(h2){ var n = document.createElement('span'); n.className = 'conteo'; n.textContent = filas.length; h2.appendChild(n); }

      var q = document.createElement('input');
      q.type = 'search'; q.className = 'buscar'; q.placeholder = 'Buscar...';
      card.querySelector('.table-scroll').before(q);
      q.addEventListener('input', function(){
        var v = q.value.toLowerCase();
        filas.forEach(function(r){ r.style.display = r.textContent.toLowerCase().indexOf(v) > -1 ? '' : 'none'; });
      });

      t.querySelectorAll('td').forEach(function(td){
        if(td.textContent.length > 140 && !td.querySelector('form,a,button')){
          var d = document.createElement('div'); d.className = 'clamp';
          d.innerHTML = td.innerHTML; td.innerHTML = ''; td.appendChild(d);
          var b = document.createElement('button'); b.type = 'button'; b.className = 'vermas'; b.textContent = 'Ver más';
          b.onclick = function(){ d.classList.toggle('abierto'); b.textContent = d.classList.contains('abierto') ? 'Ver menos' : 'Ver más'; };
          td.appendChild(b);
        }
      });
    });
  }
  if(document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init); else init();
})();