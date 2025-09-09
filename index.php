<?php
require 'database/connect.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Realtime Chart IoT</title>
   <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
   <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
   <style>
      /* Biar list tidak kepanjangan */
      #dataListWrapper {
         max-height: 500px;
         overflow-y: auto;
      }

      /* Animasi kedap-kedip untuk list danger */
      .blink {
         animation: blinkAnim 1s infinite;
      }

      @keyframes blinkAnim {
         0% {
            opacity: 1;
         }

         50% {
            opacity: 0.4;
         }

         100% {
            opacity: 1;
         }
      }
   </style>
</head>

<body class="bg-light">
   <div class="container my-4">
      <div class="row">
         <!-- Kolom Chart -->
         <div class="col-md-8 mb-3">
            <div class="card shadow-sm mb-3">
               <div class="card-header bg-primary text-white">
                  Grafik Realtime
               </div>
               <div class="card-body">
                  <canvas id="myChart"></canvas>
               </div>
            </div>

            <!-- Card Statistik -->
            <div class="row text-center">
               <div class="col-md-4 mb-3">
                  <div class="card shadow-sm border-danger">
                     <div class="card-body">
                        <h6 class="text-danger">Suhu Tertinggi</h6>
                        <h3 id="maxTemp">- °C</h3>
                     </div>
                  </div>
               </div>
               <div class="col-md-4 mb-3">
                  <div class="card shadow-sm border-primary">
                     <div class="card-body">
                        <h6 class="text-primary">Suhu Terendah</h6>
                        <h3 id="minTemp">- °C</h3>
                     </div>
                  </div>
               </div>
               <div class="col-md-4 mb-3">
                  <div class="card shadow-sm border-success">
                     <div class="card-body">
                        <h6 class="text-success">Suhu Rata-rata</h6>
                        <h3 id="avgTemp">- °C</h3>
                     </div>
                  </div>
               </div>
            </div>
         </div>

         <!-- Kolom List -->
         <div class="col-md-4 mb-3">
            <div class="card shadow-sm">
               <div class="card-header bg-success text-white">
                  Data Realtime (20 Terbaru)
               </div>
               <div id="dataListWrapper" class="card-body p-0">
                  <ol id="dataList" class="list-group list-group-numbered">
                     <!-- isi via JS -->
                  </ol>
               </div>
            </div>
         </div>
      </div>
   </div>

   <script>
      let chart;
      let alerted = false; // flag untuk cegah alert berulang

      async function fetchData() {
         try {
            let response = await fetch("http://localhost/iotlearning/api/getdata.php");
            let json = await response.json();

            if (json.status === 200) {
               let lastData = json.data.slice(-20); // Ambil data terakhir max 20 record

               let labels = lastData.map(item => item.created_at);
               let temps = lastData.map(item => parseFloat(item.temp));
               let humds = lastData.map(item => parseFloat(item.humd));

               updateChart(labels, temps, humds);
               updateList(lastData);
               updateStats(temps);
            } else {
               console.error("Error API:", json.message);
            }
         } catch (err) {
            console.error("Fetch error:", err);
         }
      }

      function initChart() {
         const ctx = document.getElementById('myChart').getContext('2d');
         chart = new Chart(ctx, {
            type: 'line',
            data: {
               labels: [],
               datasets: [{
                     label: 'Temperature (°C)',
                     data: [],
                     borderColor: 'red',
                     backgroundColor: 'rgba(255,0,0,0.2)',
                     borderWidth: 2,
                     tension: 0.3
                  },
                  {
                     label: 'Humidity (%)',
                     data: [],
                     borderColor: 'blue',
                     backgroundColor: 'rgba(0,0,255,0.2)',
                     borderWidth: 2,
                     tension: 0.3
                  }
               ]
            },
            options: {
               responsive: true,
               animation: false,
               scales: {
                  y: {
                     beginAtZero: true
                  }
               }
            }
         });
      }

      function updateChart(labels, temps, humds) {
         chart.data.labels = labels;
         chart.data.datasets[0].data = temps;
         chart.data.datasets[1].data = humds;
         chart.update();
      }

      function updateList(data) {
         const listEl = document.getElementById('dataList');
         listEl.innerHTML = "";

         data.forEach(item => {
            let li = document.createElement("li");

            if (parseFloat(item.temp) > 28) {
               li.className = "list-group-item list-group-item-danger blink d-flex justify-content-between align-items-start";
            } else {
               li.className = "list-group-item d-flex justify-content-between align-items-start";
            }

            li.innerHTML = `
               <div class="ms-2 me-auto">
                  <div class="fw-bold">Waktu: ${item.created_at}</div>
                  Suhu: ${item.temp} °C | Kelembaban: ${item.humd} %
               </div>
            `;
            listEl.appendChild(li);
         });
      }

      function updateStats(temps) {
         if (temps.length === 0) return;

         let max = Math.max(...temps);
         let min = Math.min(...temps);
         let avg = (temps.reduce((a, b) => a + b, 0) / temps.length).toFixed(2);

         document.getElementById("maxTemp").textContent = max + " °C";
         document.getElementById("minTemp").textContent = min + " °C";
         document.getElementById("avgTemp").textContent = avg + " °C";

         // Trigger alert jika suhu >= 33
         if (max >= 35 && !alerted) {
            alerted = true; // supaya alert tidak muncul terus
            alert("⚠️ Peringatan! Suhu sudah mencapai " + max + " °C");
         }

         // Reset flag kalau suhu normal lagi
         if (max < 33) {
            alerted = false;
         }
      }

      // Inisialisasi chart
      initChart();
      fetchData();
      setInterval(fetchData, 5000);
   </script>
</body>

</html>