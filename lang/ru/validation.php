<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted' => ':attribute должно быть принято.',
    'accepted_if' => ':attribute должно быть принято, когда :other равно :value.',
    'active_url' => ':attribute должно быть действительным URL.',
    'after' => ':attribute должно быть датой после :date.',
    'after_or_equal' => ':attribute должно быть датой после или равной :date.',
    'alpha' => ':attribute должно содержать только буквы.',
    'alpha_dash' => ':attribute должно содержать только буквы, цифры, тире и подчеркивания.',
    'alpha_num' => ':attribute должно содержать только буквы и цифры.',
    'array' => ':attribute должно быть массивом.',
    'ascii' => ':attribute должно содержать только однобайтовые алфавитно-цифровые символы и символы.',
    'before' => ':attribute должно быть датой до :date.',
    'before_or_equal' => ':attribute должно быть датой до или равной :date.',

    'between' => [
        'array' => ':attribute должно содержать от :min до :max элементов.',
        'file' => ':attribute должно быть между :min и :max килобайтами.',
        'numeric' => ':attribute должно быть между :min и :max.',
        'string' => ':attribute должно быть от :min до :max символов.',
    ],

    'boolean' => ':attribute должно быть true или false.',
    'can' => ':attribute содержит недопустимое значение.',
    'confirmed' => 'Подтверждение :attribute не совпадает.',
    'current_password' => 'Неправильный пароль.',
    'date' => ':attribute должно быть действительной датой.',
    'date_equals' => ':attribute должно быть датой, равной :date.',
    'date_format' => ':attribute должно соответствовать формату :format.',
    'decimal' => ':attribute должно иметь :decimal десятичных знаков.',
    'declined' => ':attribute должно быть отклонено.',
    'declined_if' => ':attribute должно быть отклонено, когда :other равно :value.',
    'different' => ':attribute и :other должны отличаться.',
    'digits' => ':attribute должно быть :digits цифр.',
    'digits_between' => ':attribute должно быть между :min и :max цифрами.',
    'dimensions' => ':attribute имеет недопустимые размеры изображения.',
    'distinct' => ':attribute имеет повторяющееся значение.',
    'doesnt_end_with' => ':attribute не должно заканчиваться ни одним из следующих значений: :values.',
    'doesnt_start_with' => ':attribute не должно начинаться ни с одного из следующих значений: :values.',
    'email' => ':attribute должно быть действительным адресом электронной почты.',
    'ends_with' => ':attribute должно заканчиваться одним из следующих значений: :values.',
    'enum' => 'Выбранное значение :attribute недопустимо.',
    'exists' => 'Выбранное значение :attribute недействительно.',
    'extensions' => ':attribute должно иметь одно из следующих расширений: :values.',
    'file' => ':attribute должно быть файлом.',
    'filled' => ':attribute должно иметь значение.',

    'gt' => [
        'array' => ':attribute должно содержать более :value элементов.',
        'file' => ':attribute должно быть больше :value килобайт.',
        'numeric' => ':attribute должно быть больше :value.',
        'string' => ':attribute должно содержать больше :value символов.',
    ],

    'gte' => [
        'array' => ':attribute должно содержать :value элементов или больше.',
        'file' => ':attribute должно быть больше или равно :value килобайт.',
        'numeric' => ':attribute должно быть больше или равно :value.',
        'string' => ':attribute должно быть больше или равно :value символов.',
    ],

    'hex_color' => ':attribute должно быть допустимым шестнадцатеричным цветом.',
    'image' => ':attribute должно быть изображением.',
    'in' => 'Выбранное значение для :attribute недопустимо.',
    'in_array' => ':attribute должно существовать в :other.',
    'integer' => ':attribute должно быть целым числом.',
    'ip' => ':attribute должно быть действительным IP-адресом.',
    'ipv4' => ':attribute должно быть действительным IPv4-адресом.',
    'ipv6' => ':attribute должно быть действительным IPv6-адресом.',
    'json' => ':attribute должно быть допустимой JSON строкой.',
    'lowercase' => ':attribute должно быть в нижнем регистре.',

    'lt' => [
        'array' => ':attribute должно содержать менее :value элементов.',
        'file' => ':attribute должно быть меньше :value килобайт.',
        'numeric' => ':attribute должно быть меньше :value.',
        'string' => ':attribute должно содержать меньше :value символов.',
    ],

    'lte' => [
        'array' => ':attribute не должно содержать более :value элементов.',
        'file' => ':attribute должно быть меньше или равно :value килобайт.',
        'numeric' => ':attribute должно быть меньше или равно :value.',
        'string' => ':attribute должно быть меньше или равно :value символов.',
    ],

    'mac_address' => ':attribute должно быть допустимым MAC-адресом.',

    'max' => [
        'array' => ':attribute не должно содержать более :max элементов.',
        'file' => ':attribute не должно быть больше :max килобайт.',
        'numeric' => ':attribute не должно быть больше :max.',
        'string' => ':attribute не должно быть больше :max символов.',
    ],

    'max_digits' => ':attribute не должно содержать более :max цифр.',
    'mimes' => ':attribute должно быть файлом одного из следующих типов: :values.',
    'mimetypes' => ':attribute должно быть файлом одного из следующих типов: :values.',

    'min' => [
        'array' => ':attribute должно содержать как минимум :min элементов.',
        'file' => ':attribute должно быть как минимум :min килобайт.',
        'numeric' => ':attribute должно быть как минимум :min.',
        'string' => ':attribute должно быть как минимум :min символов.',
    ],

    'min_digits' => ':attribute должно содержать как минимум :min цифр.',
    'missing' => ':attribute должно отсутствовать.',
    'missing_if' => ':attribute должно отсутствовать, когда :other равно :value.',
    'missing_unless' => ':attribute должно отсутствовать, если :other не равно :value.',
    'missing_with' => ':attribute должно отсутствовать, когда :values присутствует.',
    'missing_with_all' => ':attribute должно отсутствовать, когда :values присутствуют.',
    'multiple_of' => ':attribute должно быть кратным :value.',
    'not_in' => 'Выбранное значение для :attribute недопустимо.',
    'not_regex' => 'Формат :attribute недопустим.',
    'numeric' => ':attribute должно быть числом.',

    'password' => [
        'letters' => ':attribute должно содержать как минимум одну букву.',
        'mixed' => ':attribute должно содержать как минимум одну заглавную и одну строчную букву.',
        'numbers' => ':attribute должно содержать как минимум одну цифру.',
        'symbols' => ':attribute должно содержать как минимум один символ.',
        'uncompromised' => 'Указанное значение :attribute встречается в утечках данных. Пожалуйста, выберите другое значение :attribute.',
    ],

    'present' => ':attribute должно присутствовать.',
    'present_if' => ':attribute должно присутствовать, когда :other равно :value.',
    'present_unless' => ':attribute должно присутствовать, если :other не равно :value.',
    'present_with' => ':attribute должно присутствовать, когда :values присутствует.',
    'present_with_all' => ':attribute должно присутствовать, когда :values присутствуют.',
    'prohibited' => ':attribute запрещено.',
    'prohibited_if' => ':attribute запрещено, когда :other равно :value.',
    'prohibited_unless' => ':attribute запрещено, если :other находится в :values.',
    'prohibits' => ':attribute запрещает наличие :other.',
    'regex' => 'Формат :attribute недопустим.',
    'required' => ':attribute обязательно для заполнения.',
    'required_array_keys' => ':attribute должно содержать записи для: :values.',
    'required_if' => ':attribute обязательно для заполнения, когда :other равно :value.',
    'required_if_accepted' => ':attribute обязательно для заполнения, когда :other принято.',
    'required_unless' => ':attribute обязательно для заполнения, если :other не находится в :values.',
    'required_with' => ':attribute обязательно для заполнения, когда :values присутствует.',
    'required_with_all' => ':attribute обязательно для заполнения, когда :values присутствуют.',
    'required_without' => ':attribute обязательно для заполнения, когда :values отсутствует.',
    'required_without_all' => ':attribute обязательно для заполнения, когда отсутствуют все значения :values.',
    'same' => ':attribute должно совпадать с :other.',

    'size' => [
        'array' => ':attribute должно содержать :size элементов.',
        'file' => ':attribute должно быть :size килобайт.',
        'numeric' => ':attribute должно быть :size.',
        'string' => ':attribute должно быть :size символов.',
    ],

    'starts_with' => ':attribute должно начинаться с одного из следующих значений: :values.',
    'string' => ':attribute должно быть строкой.',
    'timezone' => ':attribute должно быть допустимым часовым поясом.',
    'unique' => ':attribute уже занято.',
    'uploaded' => 'Не удалось загрузить :attribute.',
    'uppercase' => ':attribute должно быть в верхнем регистре.',
    'url' => ':attribute должно быть допустимым URL-адресом.',
    'ulid' => ':attribute должно быть допустимым ULID.',
    'uuid' => ':attribute должно быть допустимым UUID.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'пользовательское сообщение',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [],

];
