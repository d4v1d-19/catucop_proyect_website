# Playas y destinos fuera de ruta — Península de Osa
### Investigación para enriquecer `mapa-3d-osa.html` · agosto 2026

42 sitios documentados: 24 playas, 2 cataratas, 4 reservas privadas, 6 sitios comunitarios, estaciones de Corcovado, un humedal, un mirador y dos proyectos de conservación de tortugas. El detalle completo está en `osa_sitios.json`, con coordenadas en WGS84 y en CRTM05 (EPSG:5367) listas para el mapa.

---

## 1. Lo primero: la regla legal que ordena todo lo demás

La Ley 6043 (Zona Marítimo Terrestre) divide los 200 m desde la pleamar ordinaria en **zona pública** (los primeros 50 m, inalienable y de libre tránsito) y **zona restringida** (los 150 m siguientes, que solo se usan por concesión municipal). Los manglares y esteros son zona pública sin importar su extensión.

Traducido al mapa: **ninguna playa de Osa es privada**. Pero eso no significa que se pueda llegar a todas. Nadie tiene derecho a cruzar propiedad privada para alcanzar la zona pública, y en Osa el acceso terrestre a varias playas pasa por fincas, lodges o caminos de servidumbre. Esa es exactamente la distinción que el mapa debería comunicar.

Por eso el dataset usa siete categorías de acceso en lugar de un simple abierto/cerrado:

| Categoría | Qué significa | Sitios |
|---|---|---|
| `publico` | Camino público, sin cobro ni permiso | 2 |
| `publico_condicionado` | Dominio público, pero condicionado por 4x4, marea, ríos o caminos malos | 19 |
| `parque_nacional` | Corcovado: guía certificado, reserva y cupo | 6 |
| `comunitario` | Gestionado por asociación o familia local; requiere contacto previo | 6 |
| `privado_con_cita` | Propiedad privada que sí recibe externos, con reserva | 5 |
| `solo_huespedes` | Uso exclusivo de huéspedes del hotel | 3 |
| `fuera_de_alcance` | Fuera del recorte cartográfico actual | 1 |

---

## 2. Los tres sitios que hay que tratar con más cuidado

**Lapa Ríos.** Es el caso más delicado. La reserva y sus senderos son de uso exclusivo de huéspedes registrados, y el hotel publica una política de derecho de admisión que prohíbe expresamente el ingreso de visitas ocasionales, incluso acompañando a un huésped. Si aparece en el mapa debe ser como **alojamiento afiliado**, nunca como atractivo visitable ni como punto de partida de sendero. Enviar visitantes al portón sería un problema para la cámara y para el hotel.

**Bosque del Cabo.** Misma lógica, algo menos rígida. Sus senderos (Zapatero, Titi, Trogón) aparecen en las plataformas de senderismo rotulados como propiedad privada. Vale la pena preguntarles directamente si aceptan visitas de día y con qué condiciones, pero mientras no haya respuesta escrita, no publicar las rutas internas como abiertas.

**El Remanso.** Lodge con catarata y rápel dentro de la finca; las actividades están armadas para huéspedes. Tratar igual: afiliado, no destino.

En los tres casos el campo `advertencia_mapa` del JSON ya trae el texto que conviene mostrar.

Un cuarto caso, distinto: **Osa Conservation** (campus Piro / Cerro Osa) no es un hotel sino una estación científica. Recibe gente, pero por programas de voluntariado, cursos y estancias coordinadas. Tampoco es de entrada libre.

---

## 3. Catarata King Louis (Catarata Matapalo)

Es el destino fuera de ruta más buscado del sector sur y merece una ficha detallada, porque casi toda la información que circula está desactualizada.

- **Coordenada:** 8.3829327, -83.2864899 (CRTM05: 578578, 926981)
- **Acceso:** público, pero condicionado. Es uno de los poquísimos senderos de acceso libre en Matapalo; casi todos los demás caminos de la zona son privados.
- **Cómo llegar:** desde Puerto Jiménez por la ruta 245 hacia el sur (unos 40 min). En Playa Pan Dulce se toma el desvío a la izquierda hacia Cabo Matapalo. Si se llega a Lapa Ríos, ya se pasó. El camino tiene piedras grandes y quebradas que se cruzan: **4x4 con buen despeje todo el año**. Antes de llegar a la playa, se toma a la derecha hacia el parqueo, que es informal, tiene espacio para uno o dos carros y **no tiene guarda**.
- **El cambio importante:** la ruta clásica remontando el cauce del río quedó peligrosa después de una tormenta en 2022. Hoy se usa un **sendero nuevo por encima del río**, que sale sin rótulo a la derecha del camino de concreto, pasando el letrero viejo de "Catarata". Es angosto, con caída al barranco, y termina en un cruce de río con una cuerda para bajar. Unos 15 minutos. **No recomendable con niños pequeños.**
- **Estacionalidad:** la catarata se seca. Suele llevar buen caudal de mayo a enero o febrero, y quedar seca desde mediados de febrero hasta abril. Si alguien pregunta en esos meses, la recomendación honesta es consultar a un local antes de manejar dos horas.
- **Altura:** las fuentes la reportan entre 25 y 30 m (90-100 pies). Vale la pena que la cámara fije un dato oficial.

Sin agua, sin baños, sin señal celular, sin comercio. Combina bien con Playa Pan Dulce o Playa Carbonera de regreso.

---

## 4. Playas por sector

### Golfo Dulce — Puerto Jiménez
Aguas más calmas, arena oscura, poca gente.

- **Platanares / Preciosa** — los vecinos usan los dos nombres para la misma franja continua; conviene decidir cómo rotularla. Es la playa más accesible desde el pueblo (10 min de lastre) y la de mayor valor de producto: anidación de lora y carey, manglar detrás, liberaciones al amanecer con Tortugas Preciosas de Osa (+506 8413 5194, aporte reportado ~USD 35). Rompiente directa en la orilla: precaución al bañarse.
- **Punta Arenitas** — espiga frente al pueblo, solo por bote o kayak. Agua muy calma, delfines y mantas.
- **Playa Tamales** — 25 min en carro, accesible con sedán en verano. Orilla y fondo de piedra: **no apta para nadar**, pero es el punto de pesca de orilla tradicional.
- **Playa Sándalo** y **Playa Blanca (Palma)** — coordenadas poco confiables (Google las devuelve como dirección y como camino, no como playa). Marcadas para verificar en campo antes de publicar.

### Cabo Matapalo
Tres playas seguidas en la punta sur, todas con 4x4.

- **Pan Dulce** — la mejor del cabo para bañarse y el point break de derecha más conocido de Osa; con marejada del sur la ola corre más de 300 m. Mejor con marea bajando (en baja extrema el fondo es muy rocoso).
- **Carbonera** — la ensenada de agua más azul; buena para baño y kiteboard, con resaca cuando hay oleaje.
- **Matapalo** — surf avanzado, canto rodado, es donde arranca el sendero a la catarata.

**Nota cultural que vale oro para la cámara:** los visitantes extranjeros llaman "Backwash" a Playa Matapalo, y hay reclamos públicos de vecinos diciendo que el nombre local es **Bahía Chocuana** y que cambiarlo les molesta. Rotular con el nombre local primero y el extranjero como alias es un detalle pequeño que dice mucho sobre quién hizo el mapa.

### Costa Pacífica — Piro a Carate
La franja más salvaje y la más sensible ecológicamente.

- **Piro** — acceso a pie que pasa por terrenos de Osa Conservation; mejor coordinar o ir con guía. Corrientes fuertes y cocodrilos en la desembocadura: no es playa de baño.
- **Pejeperro** — junto al Humedal Nacional Pejeperro, sitio importante de anidación de lora. **La coordenada de Google no es confiable** (el pin está clasificado como "playground"): hay que confirmarla con la ADI antes de publicar. No visitar sin guía.
- **Río Oro** — playa pública, pero el uso nocturno lo ordena COPROT (+506 8456 9201), que hace los patrullajes. Caminar de noche por cuenta propia interfiere con la anidación. Este es un caso donde el mapa debería decir explícitamente "solo con el proyecto".
- **Carate** — de 1.5 a 3 horas desde Puerto Jiménez con 2 o 3 cruces de río; en lluvia pueden cerrar el paso. Playa kilométrica y prácticamente desierta, con lapas, coatíes y dantas. Cocodrilos en la laguna, resaca fuerte, y llega bastante basura marina (dato honesto que conviene no esconder). Es la puerta a La Leona: 45 min caminando por la playa.

### Bahía Drake
Sector con más servicios y el mejor producto caminable de la península.

- **Colorada** — la playa del pueblo de Agujitas, ~1 km, atardeceres. Es zona de embarque de los tours: se llena en la mañana y a media tarde, y no conviene bañarse donde entran las lanchas.
- **Cocalito** — una hora a pie desde Agujitas. La ola rompe justo en la orilla y puede ser fuerte; sin salvavidas.
- **Las Caletas** — parada intermedia del sendero costero. Ojo: hay un hotel homónimo, conviene aclarar en el mapa que el punto es la playa.
- **San Josecito** — **la mejor del sector para bañarse y hacer snorkel.** Agua turquesa, sombra de palmeras, lodges que venden almuerzo, lapas rojas. Parada habitual de los tours a Isla del Caño. Se llega caminando (2.5-3 h desde Agujitas), en bote, o en 4x4 hasta Rincón de San Josecito y 30 min a pie.
- **Rincón de San Josecito** — buen punto de parqueo, pero **corrientes de retorno peligrosas**. Advertencia explícita: para bañarse, caminar 15 min al norte hasta San Josecito.
- **Ganado** y **Violín** — costa norte, muy solitarias. Ganado tiene pendiente pronunciada y oleaje fuerte, no apta para nadar. Violín está cerca de la desembocadura del Sierpe (cocodrilos) y se usa para camping. Ambas con pocas reseñas: coordenadas a verificar.

**El sendero costero Agujitas → San Josecito** merece figurar como producto propio, no solo como línea. Pasa frente a hoteles y luego entra a bosque, con varias playas encadenadas. Es donde más fauna reporta la gente en toda la costa: lapas, monos, tucanes, coatíes. Salir temprano por el calor.

### Playas dentro de Corcovado
**Madrigal, Llorona y Sirena** solo se visitan con guía certificado y reserva ante el SINAC, con cupo diario limitado. En Sirena hay revisión de equipaje al ingresar: no se permite comida, plástico de un solo uso, cigarrillos ni armas.

⚠️ **Playa Madrigal:** las reseñas de Google bajo ese nombre corresponden a otra Playa Madrigal del Pacífico Central (mencionan duchas públicas y el mirador Miro). La coordenada sí cae en la costa de Osa, pero **el contenido descriptivo de Google no sirve** para esta ficha.

---

## 5. Otros destinos fuera de ruta que valen la pena

Los que más rendimiento le darían al mapa, porque son visitables, están subrepresentados y son afiliables:

- **Dos Brazos de Río Tigre** (+506 8691 4545) — pueblo minero convertido en referente de turismo comunitario, con un salón ecocultural de bambú que funciona como centro de información y contratación de guías. Lun-Sáb 9:00-17:00.
- **Sector El Tigre de Corcovado** — el único sector gestionado con participación comunitaria, la entrada más barata al parque y la única accesible sin bote. Sendero exigente, muy bueno para aves endémicas de Osa, y normalmente sin otros grupos. Llevar 2 litros de agua por persona.
- **Rancho Quemado** (+506 8558 1822) — turismo rural en el interior: hospedaje familiar, comida casera, caminatas.
- **Finca Las Minas, tour de oro artesanal** (+506 8607 5093, 6:00-15:00) — historia oral de la minería de Osa y práctica de oreo con batea. Reservar y pedir traductor si el grupo no habla español.
- **Laguna Chocuaco** (+506 8897 4926) — humedal casi sin visitación, excelente para aves acuáticas y para el chocuaco que le da nombre. Acceso organizado por la comunidad.
- **Finca Köbö** (+506 8398 7604) — tour de cacao de la semilla al chocolate, jardín botánico y refugio de murciélagos. **Lun-Vie 9:30-16:30, cerrado fines de semana**: es un dato que conviene mostrar, porque mucha gente llega en sábado.
- **Reserva Natural Río Nuevo** (+506 8559 7625) — reserva privada abierta con tour guiado, a corta distancia de Puerto Jiménez; hay registros de rastros de jaguar.
- **Catarata de Bahía Drake** — el acceso cruza fincas y se reporta un cobro de alrededor de USD 10 por persona. Lo más cómodo es a caballo desde Agujitas. El último tramo se convierte en lodazal por el paso de caballos. **Hay que confirmar quién administra y cobra actualmente antes de publicarla.**
- **Mirador Osa** (+506 6108 9573) — parada panorámica sobre la carretera a 18 km de Chacarita, con vista al golfo y a la sierra de Corcovado. Es el primer alto natural al entrar a la península.

---

## 6. Pendientes de verificación antes de publicar

Marcados en el JSON con el campo `verificar`:

1. **Playa Pejeperro** — coordenada dudosa (pin de Google mal clasificado).
2. **Playa Sándalo** y **Playa Blanca (Palma)** — Google devuelve dirección y camino, no playa.
3. **Playa Piro** — dos pines distintos en Google; decidir cuál usar.
4. **Playa Madrigal** — no usar el contenido de reseñas de Google.
5. **Playa Violín** y **Playa Sombrero** — muy pocas reseñas; confirmar coordenada y estado del acceso.
6. **Playa Matapalo** — decidir el rótulo (Bahía Chocuana vs. Backwash) con la cámara y la ADI de Matapalo.
7. **Catarata King Louis** — estado del sendero y de los puentes de la ruta 245 en 2026; si alguien administra hoy el parqueo.
8. **Catarata de Bahía Drake** — quién cobra y cuánto.
9. **Bosque del Cabo** — confirmar por escrito si aceptan visitas de día.
10. **Altura oficial de la King Louis** — las fuentes van de 25 a 30 m.

---

## 7. Cómo integrarlo al mapa

Sugerencias concretas para `mapa-3d-osa.html`:

- **Un campo `acceso` visible en la tarjeta**, con chip de color: verde para público, ámbar para condicionado o con reserva, rojo para solo huéspedes. Es la información que más falta en los mapas turísticos de Osa y la que más problemas evita.
- **Un badge "requiere 4x4"** y otro **"requiere guía"**. En Osa esos dos datos deciden si el viaje es posible.
- **Campo `seguridad` propio, no mezclado con la descripción.** Varias de estas playas tienen resaca peligrosa o cocodrilos, y esa línea no debería competir por espacio con el texto de venta.
- **Estacionalidad** para la catarata y para las temporadas de anidación: una playa de tortugas en marzo y en septiembre no es el mismo producto.
- Los sitios `solo_huespedes` podrían aparecer en la capa de afiliados en lugar de en la de atractivos, reusando el modal de "Visitar sitio" que ya existe.

El JSON está estructurado para pegarse junto al objeto `AFILIADOS` sin tocar la lógica del mapa. Trae `lat`/`lon` y `crtm05` (x, y) para cada sitio, de modo que sirve tanto para los enlaces a Google Maps como para posicionar en la escena.
