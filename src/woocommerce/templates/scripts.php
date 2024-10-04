<script>
  "use strict";
  $ = jQuery;

  $(document).ready(function() {
    const postCode = $('#billing_postcode');

    const endpoint = 'https://www.streetdirectory.com/api/';

    const searchAddress = $('#search_address_button_field');

    const selectAddress = $('#billing_select_address');

    const buldingField = $('#billing_address_2');

    const streetField = $('#billing_address_1');

    //Init Button Search
    initSearchButton(postCode.val)

    postCode.on('input', function(e) {
      setTimeout(function() {
        initSearchButton(this.val, searchAddress);
      }, 1500);
    })

    //Init Button Search

    searchAddress.on('click', function(e) {
      e.preventDefault();

      setTimeout(function() {
        getAddressByPostCode(endpoint, postCode.val()).then(function({
          dataSelect
        }) {
          console.log(dataSelect); // Now this logs the correct data

          // If dataSelect is undefined or empty, return early
          if (typeof dataSelect === 'undefined' || dataSelect.length === 0) return;

          const message = `Found ${dataSelect.length} address!`;
          alert(message);
          initAddressSelect(dataSelect);
        });

      }, 1000);

    });

    // Listen SelectChange

    selectAddress.on("select2:select", function(e) {
      var data = e.params.data;
      if (data === '') return;
      buldingField.val(data.text);
      streetField.val(data.id);
    })

    function initSearchButton(el) {
      if (el === '') searchAddress.removeClass('opened')
      searchAddress.addClass('opened');
    }

    function hanleMappingDataAddress(data) {
      if (typeof data === 'undefined' || data.length === 0) return;
      const dataSelect = data.map(item => ({
        id: item.i,
        text: item.v,

      }));
      const addressData = data.map(item => ({
        id: item.v,
        text: item.v,
        apartment: item.v,
        strees: item.i

      }));
      return {
        dataSelect,
        addressData
      };
    }

    function initAddressSelect(address) {
      if (typeof address == 'undefined') return;
      $("#billing_select_address").addClass('opened')
      $("#billing_select_address").select2({
        data: address
      })
    }

    function getAddressByPostCode(endpoint, postcode, method = 'GET') {
      const params = {
        mode: 'search',
        profile: 'sd_auto',
        country: 'sg',
        state: 0,
        q: postcode,
        output: 'js',
        limit: 10,
        callback: 'set_data_search',
        level: 7,
        v: '1.0.2.820',
        not_nearby: 1,
        x: '103.83050972046',
        y: '1.304787132947',
      };

      return $.ajax({
        url: endpoint + '?' + $.param(params),
        type: method,
        dataType: 'jsonp',
      }).then(function(response) {
        console.log(response);
        const {
          dataSelect,
          addressData
        } = hanleMappingDataAddress(response);
        console.log(dataSelect);

        return {
          dataSelect,
          addressData
        };
      }).catch(function(xhr, status, error) {
        console.error("API call failed:", error);
        console.error("Status:", status);
        console.error("Response:", xhr.responseText);
        return {
          dataSelect: [],
          addressData: null
        }; // Return empty data on error
      });
    }
  });
</script>
<style>
  #search_address_button_field {
    text-align: right;
  }

  #search_address_button_field,
  #search_address_button_field * {
    height: 0;
    overflow: hidden;
    pointer-events: none;
    transition: all .2s linear;
  }

  #search_address_button_field.opened,
  #search_address_button_field.opened * {
    height: auto;
    pointer-events: all;
  }

  #billing_select_address {
    height: 0;
    border: 0px;
  }

  #billing_select_address.opened {
    transition: all .2s linear;
    border: auto;

    height: auto;

  }
</style>
