/**
 * Country Picker – vanilla JS implementation inspired by driaug/country-picker.
 * Replaces the plain-text country input on the signup form with a searchable
 * dropdown that shows a country flag next to each option.
 *
 * Flag images: https://purecatamphetamine.github.io/country-flag-icons/3x2/{code}.svg
 */
(function () {
    'use strict';

    var COUNTRIES = [
        { title: 'Afghanistan', value: 'AF' },
        { title: 'Albania', value: 'AL' },
        { title: 'Algeria', value: 'DZ' },
        { title: 'American Samoa', value: 'AS' },
        { title: 'Andorra', value: 'AD' },
        { title: 'Angola', value: 'AO' },
        { title: 'Anguilla', value: 'AI' },
        { title: 'Argentina', value: 'AR' },
        { title: 'Armenia', value: 'AM' },
        { title: 'Aruba', value: 'AW' },
        { title: 'Australia', value: 'AU' },
        { title: 'Austria', value: 'AT' },
        { title: 'Azerbaijan', value: 'AZ' },
        { title: 'Bahamas', value: 'BS' },
        { title: 'Bahrain', value: 'BH' },
        { title: 'Bangladesh', value: 'BD' },
        { title: 'Barbados', value: 'BB' },
        { title: 'Belarus', value: 'BY' },
        { title: 'Belgium', value: 'BE' },
        { title: 'Belize', value: 'BZ' },
        { title: 'Benin', value: 'BJ' },
        { title: 'Bermuda', value: 'BM' },
        { title: 'Bhutan', value: 'BT' },
        { title: 'Bolivia', value: 'BO' },
        { title: 'Bosnia and Herzegovina', value: 'BA' },
        { title: 'Botswana', value: 'BW' },
        { title: 'Brazil', value: 'BR' },
        { title: 'British Virgin Islands', value: 'VG' },
        { title: 'Brunei', value: 'BN' },
        { title: 'Bulgaria', value: 'BG' },
        { title: 'Burkina Faso', value: 'BF' },
        { title: 'Burundi', value: 'BI' },
        { title: 'Cambodia', value: 'KH' },
        { title: 'Cameroon', value: 'CM' },
        { title: 'Canada', value: 'CA' },
        { title: 'Cape Verde', value: 'CV' },
        { title: 'Cayman Islands', value: 'KY' },
        { title: 'Central African Republic', value: 'CF' },
        { title: 'Chad', value: 'TD' },
        { title: 'Chile', value: 'CL' },
        { title: 'China', value: 'CN' },
        { title: 'Colombia', value: 'CO' },
        { title: 'Comoros', value: 'KM' },
        { title: 'Cook Islands', value: 'CK' },
        { title: 'Costa Rica', value: 'CR' },
        { title: 'Croatia', value: 'HR' },
        { title: 'Cuba', value: 'CU' },
        { title: 'Curacao', value: 'CW' },
        { title: 'Cyprus', value: 'CY' },
        { title: 'Czech Republic', value: 'CZ' },
        { title: 'Democratic Republic of the Congo', value: 'CD' },
        { title: 'Denmark', value: 'DK' },
        { title: 'Djibouti', value: 'DJ' },
        { title: 'Dominica', value: 'DM' },
        { title: 'Dominican Republic', value: 'DO' },
        { title: 'East Timor', value: 'TL' },
        { title: 'Ecuador', value: 'EC' },
        { title: 'Egypt', value: 'EG' },
        { title: 'El Salvador', value: 'SV' },
        { title: 'Eritrea', value: 'ER' },
        { title: 'Estonia', value: 'EE' },
        { title: 'Ethiopia', value: 'ET' },
        { title: 'Faroe Islands', value: 'FO' },
        { title: 'Fiji', value: 'FJ' },
        { title: 'Finland', value: 'FI' },
        { title: 'France', value: 'FR' },
        { title: 'French Polynesia', value: 'PF' },
        { title: 'Gabon', value: 'GA' },
        { title: 'Gambia', value: 'GM' },
        { title: 'Georgia', value: 'GE' },
        { title: 'Germany', value: 'DE' },
        { title: 'Ghana', value: 'GH' },
        { title: 'Greece', value: 'GR' },
        { title: 'Greenland', value: 'GL' },
        { title: 'Grenada', value: 'GD' },
        { title: 'Guam', value: 'GU' },
        { title: 'Guatemala', value: 'GT' },
        { title: 'Guernsey', value: 'GG' },
        { title: 'Guinea', value: 'GN' },
        { title: 'Guinea-Bissau', value: 'GW' },
        { title: 'Guyana', value: 'GY' },
        { title: 'Haiti', value: 'HT' },
        { title: 'Honduras', value: 'HN' },
        { title: 'Hong Kong', value: 'HK' },
        { title: 'Hungary', value: 'HU' },
        { title: 'Iceland', value: 'IS' },
        { title: 'India', value: 'IN' },
        { title: 'Indonesia', value: 'ID' },
        { title: 'Iran', value: 'IR' },
        { title: 'Iraq', value: 'IQ' },
        { title: 'Ireland', value: 'IE' },
        { title: 'Isle of Man', value: 'IM' },
        { title: 'Israel', value: 'IL' },
        { title: 'Italy', value: 'IT' },
        { title: 'Ivory Coast', value: 'CI' },
        { title: 'Jamaica', value: 'JM' },
        { title: 'Japan', value: 'JP' },
        { title: 'Jersey', value: 'JE' },
        { title: 'Jordan', value: 'JO' },
        { title: 'Kazakhstan', value: 'KZ' },
        { title: 'Kenya', value: 'KE' },
        { title: 'Kiribati', value: 'KI' },
        { title: 'Kosovo', value: 'XK' },
        { title: 'Kuwait', value: 'KW' },
        { title: 'Kyrgyzstan', value: 'KG' },
        { title: 'Laos', value: 'LA' },
        { title: 'Latvia', value: 'LV' },
        { title: 'Lebanon', value: 'LB' },
        { title: 'Lesotho', value: 'LS' },
        { title: 'Liberia', value: 'LR' },
        { title: 'Libya', value: 'LY' },
        { title: 'Liechtenstein', value: 'LI' },
        { title: 'Lithuania', value: 'LT' },
        { title: 'Luxembourg', value: 'LU' },
        { title: 'Macau', value: 'MO' },
        { title: 'Macedonia', value: 'MK' },
        { title: 'Madagascar', value: 'MG' },
        { title: 'Malawi', value: 'MW' },
        { title: 'Malaysia', value: 'MY' },
        { title: 'Maldives', value: 'MV' },
        { title: 'Mali', value: 'ML' },
        { title: 'Malta', value: 'MT' },
        { title: 'Marshall Islands', value: 'MH' },
        { title: 'Mauritania', value: 'MR' },
        { title: 'Mauritius', value: 'MU' },
        { title: 'Mayotte', value: 'YT' },
        { title: 'Mexico', value: 'MX' },
        { title: 'Micronesia', value: 'FM' },
        { title: 'Moldova', value: 'MD' },
        { title: 'Monaco', value: 'MC' },
        { title: 'Mongolia', value: 'MN' },
        { title: 'Montenegro', value: 'ME' },
        { title: 'Morocco', value: 'MA' },
        { title: 'Mozambique', value: 'MZ' },
        { title: 'Myanmar', value: 'MM' },
        { title: 'Namibia', value: 'NA' },
        { title: 'Nepal', value: 'NP' },
        { title: 'Netherlands', value: 'NL' },
        { title: 'Netherlands Antilles', value: 'AN' },
        { title: 'New Caledonia', value: 'NC' },
        { title: 'New Zealand', value: 'NZ' },
        { title: 'Nicaragua', value: 'NI' },
        { title: 'Niger', value: 'NE' },
        { title: 'Nigeria', value: 'NG' },
        { title: 'North Korea', value: 'KP' },
        { title: 'Northern Mariana Islands', value: 'MP' },
        { title: 'Norway', value: 'NO' },
        { title: 'Oman', value: 'OM' },
        { title: 'Pakistan', value: 'PK' },
        { title: 'Palestine', value: 'PS' },
        { title: 'Panama', value: 'PA' },
        { title: 'Papua New Guinea', value: 'PG' },
        { title: 'Paraguay', value: 'PY' },
        { title: 'Peru', value: 'PE' },
        { title: 'Philippines', value: 'PH' },
        { title: 'Poland', value: 'PL' },
        { title: 'Portugal', value: 'PT' },
        { title: 'Puerto Rico', value: 'PR' },
        { title: 'Qatar', value: 'QA' },
        { title: 'Republic of the Congo', value: 'CG' },
        { title: 'Reunion', value: 'RE' },
        { title: 'Romania', value: 'RO' },
        { title: 'Russia', value: 'RU' },
        { title: 'Rwanda', value: 'RW' },
        { title: 'Saint Kitts and Nevis', value: 'KN' },
        { title: 'Saint Lucia', value: 'LC' },
        { title: 'Saint Martin', value: 'MF' },
        { title: 'Saint Pierre and Miquelon', value: 'PM' },
        { title: 'Saint Vincent and the Grenadines', value: 'VC' },
        { title: 'Samoa', value: 'WS' },
        { title: 'San Marino', value: 'SM' },
        { title: 'Sao Tome and Principe', value: 'ST' },
        { title: 'Saudi Arabia', value: 'SA' },
        { title: 'Senegal', value: 'SN' },
        { title: 'Serbia', value: 'RS' },
        { title: 'Seychelles', value: 'SC' },
        { title: 'Sierra Leone', value: 'SL' },
        { title: 'Singapore', value: 'SG' },
        { title: 'Sint Maarten', value: 'SX' },
        { title: 'Slovakia', value: 'SK' },
        { title: 'Slovenia', value: 'SI' },
        { title: 'Solomon Islands', value: 'SB' },
        { title: 'Somalia', value: 'SO' },
        { title: 'South Africa', value: 'ZA' },
        { title: 'South Korea', value: 'KR' },
        { title: 'South Sudan', value: 'SS' },
        { title: 'Spain', value: 'ES' },
        { title: 'Sri Lanka', value: 'LK' },
        { title: 'Sudan', value: 'SD' },
        { title: 'Suriname', value: 'SR' },
        { title: 'Swaziland', value: 'SZ' },
        { title: 'Sweden', value: 'SE' },
        { title: 'Switzerland', value: 'CH' },
        { title: 'Syria', value: 'SY' },
        { title: 'Taiwan', value: 'TW' },
        { title: 'Tajikistan', value: 'TJ' },
        { title: 'Tanzania', value: 'TZ' },
        { title: 'Thailand', value: 'TH' },
        { title: 'Togo', value: 'TG' },
        { title: 'Tonga', value: 'TO' },
        { title: 'Trinidad and Tobago', value: 'TT' },
        { title: 'Tunisia', value: 'TN' },
        { title: 'Turkey', value: 'TR' },
        { title: 'Turkmenistan', value: 'TM' },
        { title: 'Turks and Caicos Islands', value: 'TC' },
        { title: 'Tuvalu', value: 'TV' },
        { title: 'U.S. Virgin Islands', value: 'VI' },
        { title: 'Uganda', value: 'UG' },
        { title: 'Ukraine', value: 'UA' },
        { title: 'United Arab Emirates', value: 'AE' },
        { title: 'United Kingdom', value: 'GB' },
        { title: 'United States', value: 'US' },
        { title: 'Uruguay', value: 'UY' },
        { title: 'Uzbekistan', value: 'UZ' },
        { title: 'Vanuatu', value: 'VU' },
        { title: 'Venezuela', value: 'VE' },
        { title: 'Vietnam', value: 'VN' },
        { title: 'Western Sahara', value: 'EH' },
        { title: 'Yemen', value: 'YE' },
        { title: 'Zambia', value: 'ZM' },
        { title: 'Zimbabwe', value: 'ZW' },
    ];

    var FLAG_BASE = 'https://purecatamphetamine.github.io/country-flag-icons/3x2/';

    function initCountryPicker() {
        var container = document.getElementById('country-picker-container');
        if (!container) { return; }

        var hiddenInput  = document.getElementById('country');
        var btn          = document.getElementById('country-picker-btn');
        var dropdown     = document.getElementById('country-picker-dropdown');
        var searchInput  = document.getElementById('country-picker-search');
        var list         = document.getElementById('country-picker-list');
        var btnFlag      = document.getElementById('country-picker-btn-flag');
        var btnGlobe     = document.getElementById('country-picker-globe-icon');
        var btnLabel     = document.getElementById('country-picker-btn-label');

        var selectedCountry = null;

        // Pre-populate when the form is re-shown after a validation error
        var preselected = hiddenInput ? hiddenInput.value.trim() : '';
        if (preselected) {
            for (var i = 0; i < COUNTRIES.length; i++) {
                if (COUNTRIES[i].title === preselected) {
                    selectedCountry = COUNTRIES[i];
                    break;
                }
            }
        }

        if (selectedCountry) {
            updateButton(selectedCountry);
        }

        function updateButton(country) {
            if (country) {
                btnFlag.src             = FLAG_BASE + country.value + '.svg';
                btnFlag.alt             = country.title;
                btnFlag.style.display   = '';
                btnGlobe.style.display  = 'none';
                btnLabel.textContent    = country.title;
                btnLabel.classList.remove('country-picker-placeholder');
            } else {
                btnFlag.style.display   = 'none';
                btnGlobe.style.display  = '';
                btnLabel.textContent    = btnLabel.dataset.placeholder;
                btnLabel.classList.add('country-picker-placeholder');
            }
        }

        function renderList(query) {
            var q = query.toLowerCase();
            var filtered = COUNTRIES.filter(function (c) {
                return c.title.toLowerCase().indexOf(q) !== -1;
            });

            list.innerHTML = '';

            if (filtered.length === 0) {
                var noResult = document.createElement('li');
                noResult.className = 'country-picker-no-results';
                noResult.textContent = 'No countries found';
                list.appendChild(noResult);
                return;
            }

            filtered.forEach(function (country) {
                var li = document.createElement('li');
                li.className = 'country-picker-option';
                if (selectedCountry && selectedCountry.value === country.value) {
                    li.classList.add('is-selected');
                }
                li.setAttribute('role', 'option');
                li.setAttribute('aria-selected', selectedCountry && selectedCountry.value === country.value ? 'true' : 'false');

                var flag = document.createElement('img');
                flag.src     = FLAG_BASE + country.value + '.svg';
                flag.alt     = '';
                flag.className = 'country-picker-option-flag';
                flag.loading = 'lazy';

                var label = document.createElement('span');
                label.textContent = country.title;

                li.appendChild(flag);
                li.appendChild(label);

                if (selectedCountry && selectedCountry.value === country.value) {
                    var check = document.createElement('span');
                    check.className = 'country-picker-check';
                    check.setAttribute('aria-hidden', 'true');
                    check.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>';
                    li.appendChild(check);
                }

                li.addEventListener('click', function () {
                    selectedCountry = country;
                    if (hiddenInput) { hiddenInput.value = country.title; }
                    updateButton(country);
                    closeDropdown();
                    searchInput.value = '';
                });

                list.appendChild(li);
            });
        }

        function openDropdown() {
            dropdown.removeAttribute('hidden');
            btn.setAttribute('aria-expanded', 'true');
            searchInput.value = '';
            renderList('');
            searchInput.focus();
        }

        function closeDropdown() {
            dropdown.setAttribute('hidden', '');
            btn.setAttribute('aria-expanded', 'false');
        }

        btn.addEventListener('click', function () {
            if (dropdown.hasAttribute('hidden')) {
                openDropdown();
            } else {
                closeDropdown();
            }
        });

        searchInput.addEventListener('input', function () {
            renderList(this.value);
        });

        searchInput.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                closeDropdown();
                btn.focus();
            }
        });

        document.addEventListener('mousedown', function (e) {
            if (!container.contains(e.target)) {
                closeDropdown();
            }
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initCountryPicker);
    } else {
        initCountryPicker();
    }
}());
