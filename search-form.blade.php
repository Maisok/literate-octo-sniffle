<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
<link rel="stylesheet" href="{{ asset('css/search-form.css') }}"> <!-- Подключение основного CSS-файла -->

<div class="ad-list">
    <form id="main-form" action="{{ route('adverts.search') }}" method="GET" data-brands-url="{{ route('get.brands') }}">
        <input type="hidden" name="city" value="{{ request()->get('city') }}">
        
        <input type="text" name="search_query" placeholder="Введите название или номер детали" 
               value="{{ request()->get('search_query') }}">
        <input type="text" id="brand-input" name="brand_input" placeholder="Введите марку">
        <select id="brand-select" name="brand_select" class="search-list_brand">
            <option value="">Выберите марку</option>
            @foreach(App\Models\BaseAvto::distinct()->pluck('brand') as $brand)
                <option value="{{ $brand }}" {{ request()->get('brand') == $brand ? 'selected' : '' }}>
                    {{ $brand }}
                </option>
            @endforeach
        </select>
        <input type="hidden" id="brand" name="brand" value="{{ request()->get('brand') }}">
        
        <!-- Добавляем текстовое поле для ввода модели -->
        <input type="text" id="model-input" name="model_input" placeholder="Введите модель">
        
        <!-- Выпадающий список для выбора модели -->
        <select id="model-select" name="model_select" class="search-list_model">
            <option value="">Выберите модель</option>
            @if(request()->get('brand')) 
                @foreach(App\Models\BaseAvto::where('brand', request()->get('brand'))->distinct()->pluck('model') as $model)
                    <option value="{{ $model }}" {{ request()->get('model') == $model ? 'selected' : '' }}>
                        {{ $model }}
                    </option>
                @endforeach
            @endif
        </select>
        <input type="hidden" id="model" name="model" value="{{ request()->get('model') }}">
        
        <select id="year" name="year">
            <option value="">Выберите год выпуска</option>
            @for($i = 2000; $i <= date('Y'); $i++)
                <option value="{{ $i }}" {{ request()->get('year') == $i ? 'selected' : '' }}>
                    {{ $i }}
                </option>
            @endfor
        </select>
        <button type="button" id="show-button">Показать</button>
    </form>

    <div id="modifications-container" class="modification">
        <label>Модификации:</label>
        <div id="modifications"></div>
    </div>
</div>

<script src="{{ asset('js/search-form.js') }}" defer></script>

<script>
    $(document).ready(function() {
        $('#show-button').on('click', function() {
            // Собираем данные из основной формы
            var formData = $('#main-form').serialize();
            
            // Добавляем данные из модификаций, если они есть
            var modifications = $('#modifications input:checked').map(function() {
                return $(this).val();
            }).get().join(',');

            if (modifications) {
                formData += '&modifications=' + encodeURIComponent(modifications);
            }

            // Перенаправляем на нужный URL с параметрами запроса
            window.location.href = '{{ route('adverts.search') }}?' + formData;
        });

        // Обработчик для выпадающего списка марок
        $('#brand-select').on('change', function() {
            var brand = $(this).val();
            $('#brand').val(brand); // Устанавливаем значение в скрытое поле
            updateModels(brand);
        });

        // Обработчик для текстового поля марок
        $('#brand-input').on('input', function() {
            var brand = $(this).val();
            $('#brand').val(brand); // Устанавливаем значение в скрытое поле
            updateModels(brand);
        });

        // Обработчик для выпадающего списка моделей
        $('#model-select').on('change', function() {
            var model = $(this).val();
            $('#model').val(model); // Устанавливаем значение в скрытое поле
            $('#model-input').val(model); // Синхронизируем с текстовым полем
        });

        // Обработчик для текстового поля моделей
        $('#model-input').on('input', function() {
            var model = $(this).val();
            $('#model').val(model); // Устанавливаем значение в скрытое поле
            $('#model-select').val(model); // Синхронизируем с выпадающим списком
        });

        function updateModels(brand) {
            if (brand) {
                $.ajax({
                    url: '{{ route('get.models') }}',
                    type: 'GET',
                    data: { brand: brand },
                    success: function(data) {
                        $('#model-select').empty();
                        $('#model-select').append('<option value="">Выберите модель</option>');
                        $.each(data, function(key, value) {
                            $('#model-select').append('<option value="' + value + '">' + value + '</option>');
                        });
                    },
                    error: function(xhr, status, error) {
                        console.error("AJAX Error: ", status, error);
                    }
                });
            } else {
                $('#model-select').empty();
                $('#model-select').append('<option value="">Выберите модель</option>');
            }
        }

        // Логика поиска
        $('#search_query').on('input', function() {
            var query = $(this).val();
            if (query.length > 0) {
                // Проверяем, является ли запрос номером детали
                if (isNumeric(query)) {
                    searchByPartNumber(query);
                } else {
                    searchByPartName(query);
                }
            }
        });

        function isNumeric(str) {
            return /^\d+$/.test(str);
        }

        function searchByPartNumber(partNumber) {
            $.ajax({
                url: '{{ route('search.by.part.number') }}',
                type: 'GET',
                data: { part_number: partNumber },
                success: function(data) {
                    displayResults(data);
                },
                error: function(xhr, status, error) {
                    console.error("AJAX Error: ", status, error);
                }
            });
        }

        function searchByPartName(partName) {
            $.ajax({
                url: '{{ route('search.by.part.name') }}',
                type: 'GET',
                data: { part_name: partName },
                success: function(data) {
                    displayResults(data);
                },
                error: function(xhr, status, error) {
                    console.error("AJAX Error: ", status, error);
                }
            });
        }

        function displayResults(data) {
            // Отображение результатов поиска
            $('#results').html(data);
        }
    });
</script>