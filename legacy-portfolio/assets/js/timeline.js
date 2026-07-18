// Código JavaScript original existente
window.addEventListener('load', () => {
   // ... (código de Isotope y otros) ...
});

// Nuevo código para el efecto flip
document.querySelectorAll('.portfolio-wrap').forEach(item => {
   // Crear contenedor flip
   const flipContainer = document.createElement('div');
   flipContainer.className = 'flip-container';

   // Mover elementos existentes al frente
   const front = document.createElement('div');
   front.className = 'front';
   while (item.firstChild) {
       front.appendChild(item.firstChild);
   }
   flipContainer.appendChild(front);

   // Crear parte trasera
   const back = document.createElement('div');
   back.className = 'back';
   back.innerHTML = `
       <div class="back-content">
           <h4>${front.querySelector('button').innerText}</h4>
           <p>Descripción del proyecto aquí</p>
           <a href="${front.querySelector('a').href}" target="_blank">Ver proyecto</a>
       </div>
   `;
   flipContainer.appendChild(back);

   // Agregar contenedor al portfolio-wrap
   item.appendChild(flipContainer);

   // Agregar evento click
   item.addEventListener('click', () => {
       item.classList.toggle('flipped');
   });
});