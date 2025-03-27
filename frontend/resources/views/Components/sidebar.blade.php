<aside class="sidebar">
    <h1 class="title">InsightEd
        <div class="aurora">
            @for ($i = 0; $i < 4; $i++)
                <div class="aurora__item"></div>
            @endfor
        </div>
    </h1>

    <div class="space-y-4">
        <div id="dropzone" class="dropzone">
            <input type="file" id="csvFile" class="hidden" accept=".csv">
            <button class="sidebar-secondary-button flex items-center">
                <img src="{{ asset('css/download.gif') }}" alt="Download" class="download-gif mr-2">
                Download Sample CSV
            </button>
            <p class="mt-1 text-sm text-gray-600">Drop CSV or click to browse</p>
        </div>

        <div class="status-indicator">
            <div></div>
            Ready to process data
        </div>

        <div class="space-y-2">
            <button class="sidebar-button" onclick="uploadCSV()">Upload & Process</button>
            <button onclick="analyzeAll()" class="w-full bg-purple-800 text-white py-3 rounded-md hover:bg-purple-900">
                Analyze All
            </button>
            <button class="sidebar-secondary-button" onclick="resetData()">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2 inline-block" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v6h6M20 20v-6h-6M9 9l6 6" />
                </svg>
                Reset Data
            </button>
        </div>
    </div>
</aside>
