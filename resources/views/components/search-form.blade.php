<div class="ad-list bg-white shadow-lg p-4 rounded-lg">
    <form id="main-form" action="{{ route('adverts.search') }}" method="GET" class="flex flex-wrap gap-4 items-center" data-brands-url="{{ route('get.brands') }}">
        <input type="hidden" name="city" value="{{ request()->get('city') }}">

        <!-- Search Query Input -->
        <input
            type="text"
            name="search_query"
            placeholder="Введите название или номер детали"
            value="{{ request()->get('search_query') }}"
            class="w-full md:w-auto flex-grow px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring focus:ring-blue-500"
        />

        <!-- Brand Input with Autocomplete -->
        <div class="relative w-full md:w-auto">
            <input
                type="text"
                id="brand-input"
                name="brand_input"
                placeholder="Введите марку"
                class="w-full md:w-auto flex-grow px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring focus:ring-blue-500"
            />
            <input type="hidden" id="brand" name="brand" value="{{ request()->get('brand') }}">
            <button type="button" id="brand-dropdown-button" class="absolute right-0 top-0 px-2 py-2 text-gray-500 focus:outline-none">▼</button>
            <div id="brand-dropdown" class="hidden absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded shadow-lg max-h-48 overflow-y-auto"></div>
        </div>
        
        <!-- Model Input with Autocomplete -->
        <div class="relative w-full md:w-auto">
            <input
                type="text"
                id="model-input"
                name="model_input"
                placeholder="Введите модель"
                class="w-full md:w-auto flex-grow px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring focus:ring-blue-500"
            />
            <input type="hidden" id="model" name="model" value="{{ request()->get('model') }}">
            <button type="button" id="model-dropdown-button" class="absolute right-0 top-0 px-2 py-2 text-gray-500 focus:outline-none">▼</button>
            <div id="model-dropdown" class="hidden absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded shadow-lg max-h-48 overflow-y-auto"></div>
        </div>

        <!-- Year Select -->
        <select
            id="year"
            name="year"
            class="w-full md:w-auto flex-grow px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring focus:ring-blue-500"
        >
            <option value="">Выберите год выпуска</option>
            @for($i = 2000; $i <= date('Y'); $i++)
            <option value="{{ $i }}" {{ request()->get('year') == $i ? 'selected' : '' }}>
                {{ $i }}
            </option>
            @endfor
        </select>

        <!-- Show Button -->
        <button
            type="button"
            id="show-button"
            class="w-full md:w-auto px-4 py-2 bg-blue-500 text-white font-semibold rounded focus:outline-none focus:ring focus:ring-blue-500"
        >
            Показать
        </button>
    </form>

    <!-- Modifications Container -->
    <div id="modifications-container" class="modification mt-4">
        <label class="font-medium">Модификации:</label>
        <div id="modifications" class="flex flex-wrap gap-2"></div>
    </div>
</div>

<!-- Import jQuery and Other JavaScript Libraries -->
<script src="{{ asset('js/search-form.js') }}" defer></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

<script>
    $(document).ready(function() {
        // Настройка автодополнения для марки
        $('#brand-input').autocomplete({
            source: function(request, response) {
                var term = request.term.trim();
                $.ajax({
                    url: '{{ route('get.brands') }}',
                    type: 'GET',
                    data: { term: term },
                    success: function(data) {
                        if (term === "") {
                            response(data); // Показываем весь список, если поле пустое
                        } else {
                            response($.ui.autocomplete.filter(data, term)); // Фильтруем список по введенному значению
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("AJAX Error: ", status, error);
                    }
                });
            },
            select: function(event, ui) {
                $('#brand').val(ui.item.value); // Устанавливаем значение в скрытое поле
                updateModels(ui.item.value);
            }
        });

        // Настройка автодополнения для модели
        $('#model-input').autocomplete({
            source: function(request, response) {
                var brand = $('#brand').val();
                var modelTerm = request.term.trim();
                $.ajax({
                    url: '{{ route('get.models') }}',
                    type: 'GET',
                    data: { term: modelTerm, brand: brand },
                    success: function(data) {
                        if (modelTerm === "") {
                            response(data); // Показываем весь список, если поле пустое
                        } else {
                            response($.ui.autocomplete.filter(data, modelTerm)); // Фильтруем список по введенному значению
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("AJAX Error: ", status, error);
                    }
                });
            },
            select: function(event, ui) {
                $('#model').val(ui.item.value); // Устанавливаем значение в скрытое поле
            }
        });

        // Обработчик для кнопки "Показать"
        $('#show-button').on('click', function() {
            var formData = $('#main-form').serialize();
            window.location.href = '{{ route('adverts.search') }}?' + formData;
        });

        function updateModels(brand) {
            $('#model-input').val(''); // Очищаем поле модели
            $('#model').val(''); // Очищаем скрытое поле модели
            $('#model-input').autocomplete("search", ""); // Сбрасываем автодополнение для модели
        }

        // Обработчик для изменения марки
        $('#brand-input').on('change', function() {
            var brand = $(this).val();
            $('#brand').val(brand); // Устанавливаем значение в скрытое поле
            updateModels(brand);
        });

        // Обработчик для изменения модели
        $('#model-input').on('input', function() {
            var model = $(this).val();
            $('#model').val(model); // Устанавливаем значение в скрытое поле
            $('#model-input').autocomplete("search", model); // Обновляем автодополнение для модели
        });

        // Обработчик для кнопки выпадающего списка марок
        $('#brand-dropdown-button').on('click', function() {
            if ($('#brand-dropdown').hasClass('hidden')) {
                $('#brand-dropdown').removeClass('hidden');
                $.ajax({
                    url: '{{ route('get.brands') }}',
                    type: 'GET',
                    success: function(data) {
                        $('#brand-dropdown').empty();
                        $.each(data, function(index, brand) {
                            $('#brand-dropdown').append('<div class="px-3 py-2 cursor-pointer hover:bg-gray-100" data-value="' + brand + '">' + brand + '</div>');
                        });
                    },
                    error: function(xhr, status, error) {
                        console.error("AJAX Error: ", status, error);
                    }
                });
            } else {
                $('#brand-dropdown').addClass('hidden');
            }
        });

        // Обработчик для выбора марки из выпадающего списка
        $('#brand-dropdown').on('click', 'div', function() {
            var brand = $(this).data('value');
            $('#brand-input').val(brand);
            $('#brand').val(brand);
            $('#brand-dropdown').addClass('hidden');
            updateModels(brand);
        });

        // Обработчик для кнопки выпадающего списка моделей
        $('#model-dropdown-button').on('click', function() {
            if ($('#model-dropdown').hasClass('hidden')) {
                $('#model-dropdown').removeClass('hidden');
                var brand = $('#brand').val();
                if (brand) {
                    $.ajax({
                        url: '{{ route('get.models') }}',
                        type: 'GET',
                        data: { brand: brand },
                        success: function(data) {
                            $('#model-dropdown').empty();
                            $.each(data, function(index, model) {
                                $('#model-dropdown').append('<div class="px-3 py-2 cursor-pointer hover:bg-gray-100" data-value="' + model + '">' + model + '</div>');
                            });
                        },
                        error: function(xhr, status, error) {
                            console.error("AJAX Error: ", status, error);
                        }
                    });
                }
            } else {
                $('#model-dropdown').addClass('hidden');
            }
        });

        // Обработчик для выбора модели из выпадающего списка
        $('#model-dropdown').on('click', 'div', function() {
            var model = $(this).data('value');
            $('#model-input').val(model);
            $('#model').val(model);
            $('#model-dropdown').addClass('hidden');
        });
    });
</script>