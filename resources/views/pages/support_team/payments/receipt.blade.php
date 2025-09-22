<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>School Receipt</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Inter', sans-serif;
    }
    .paid-stamp {
      border: 4px solid #ef4444; /* Red-500 */
      color: #ef4444;
      font-weight: 700;
      font-size: 1.5rem;
      transform: rotate(-15deg);
      padding: 0.25rem 1.5rem;
      opacity: 0.85;
      display: inline-block;
      border-radius: 0.25rem;
    }
    .receipt-container {
      max-width: 850px;
      margin: 2rem auto;
      border: 1px solid #e5e7eb;
      box-shadow: 0 8px 16px rgba(0,0,0,0.08);
      padding: 2.5rem;
      background-color: #fff;
      border-radius: 1rem;
    }
  </style>
</head>
<body class="bg-gray-100">

  <div class="receipt-container">
    <!-- Header Section -->
    <header class="flex flex-col md:flex-row justify-between items-start mb-8 border-b pb-4 gap-6">
      <div class="flex items-center space-x-4">
        <!-- Placeholder for Logo -->
        <div class="w-16 h-16 bg-gray-200 flex items-center justify-center rounded-full shadow-inner">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M12 6.253v11.494m-5.247-8.982l10.494 4.494-10.494 4.494V6.253z"/>
          </svg>
        </div>
        <div>
          <h1 class="text-2xl font-bold text-gray-800">The XXXXXXXXXX</h1>
          <p class="text-sm font-semibold text-gray-600">INTERNATIONAL SCHOOL</p>
          <div class="text-xs text-gray-500 mt-2 space-y-0.5">
            <p>📍 No-##, Colombo Road, Puttalam</p>
            <p>📞 ### ### #### / ### ### ####</p>
            <p>📧 admin@##################.lk</p>
          </div>
        </div>
      </div>
      <div class="text-right w-full md:w-auto">
        <div class="mb-2">
          <div class="flex items-center justify-end space-x-2">
            <label class="text-sm font-medium text-gray-700">Date:</label>
            <span class="font-semibold text-gray-800">{{ date('d/m/Y') }}</span>
          </div>
        </div>
        <div class="mb-2">
          <div class="flex items-center justify-end space-x-2">
            <label class="text-sm font-medium text-gray-700">Admission No:</label>
            <span class="font-semibold text-gray-800">{{ $sr->adm_no }}</span>
          </div>
          
        </div>
        <div>
          <div class="flex items-center justify-end space-x-2">
            <label class="text-sm font-medium text-gray-700">Receipt No:</label>
            <span class="font-semibold text-gray-800">{{ $pr->ref_no }}</span>
           
          </div>
        </div>
      </div>
    </header>

    <!-- Receipt Title -->
    <div class="text-center my-8">
      <h2 class="bg-black text-white inline-block px-10 py-2 text-2xl font-bold tracking-widest rounded-md shadow">
        RECEIPT
      </h2>
    </div>

    <!-- Main Content Section -->
    <main>
      <!-- Received From -->
      <div class="mb-6">
        <div class="flex justify-between items-center">
          <label class="text-md font-medium text-gray-700">Received From:</label>
          <span class="font-semibold text-gray-800">{{ $sr->user->name }}</span>
        </div>
        <div class="border-b border-gray-700 mt-1"></div>
      </div>

      <!-- Sum of Rs -->
      <div class="mb-6">
        <div class="flex justify-between items-center">
          <label class="text-md font-medium text-gray-700">Sum of Rs:</label>
          <span class="font-semibold text-gray-800">{{ $payment->amount }}</span>
        </div>
        <div class="border-b border-gray-700 mt-1"></div>
      </div>

      <!-- Month of -->
      <div class="mb-10">
        <div class="flex justify-between items-center">
          <label class="text-md font-medium text-gray-700">Month of:</label>
          <span class="font-semibold text-gray-800">{{ $month ?? '' }}</span>
        </div>
        <div class="border-b border-gray-700 mt-1"></div>
      </div>

      <!-- Payment Details -->
      <div class="flex justify-end">
        <div class="space-y-6">
          <!-- Total Due -->
          <div>
            <span class="font-medium text-gray-700">Total Due :</span>
            <div class="w-32 border-b border-black text-center">
              {{ $pr->paid ? 'CLEARED' : $pr->balance }}
            </div>
          </div>

          <!-- Paid Today -->
          <div>
            <span class="font-medium text-gray-700">Paid Today :</span>
            <div class="w-32 border-b border-black text-center">
              {{ optional($receipts->last())->amt_paid }}
            </div>
          </div>

          <!-- Balance -->
          <div>
            <span class="font-bold text-gray-800">Balance :</span>
            <div class="w-32 border-b border-black text-center">
              {{ $pr->paid ? 'CLEARED' : $pr->balance }}
            </div>
          </div>
        </div>
      </div>

      <!-- Signature Section -->
      <div class="mt-20 flex justify-between items-end">
        <div>
          <div class="flex items-center space-x-2">
            <label class="text-md font-medium text-gray-700">Year: {{ date('Y') }}</label>
          </div>
          <div class="border-b border-gray-700 mt-1 w-24"></div>
        </div>
        <div class="relative text-center">
          <div class="absolute -top-12 left-1/2 -translate-x-1/2">
            <div class="paid-stamp">PAID</div>
          </div>
          <div class="border-b border-gray-700 w-48 mt-4"></div>
          <p class="text-md font-medium text-gray-700 mt-2">Accountant</p>
        </div>
      </div>
    </main>

    <!-- Footer -->
    <footer class="text-center mt-12 text-xs text-gray-400">
      <p>Marks Trigger - ##############3</p>
    </footer>
  </div>

</body>
</html>
