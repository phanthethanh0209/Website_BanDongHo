var citis = document.getElementById("city");
var districts = document.getElementById("district");
var wards = document.getElementById("ward");
var Parameter = {
    url: "https://raw.githubusercontent.com/kenzouno1/DiaGioiHanhChinhVN/master/data.json",
    method: "GET",
    responseType: "application/json",
};
var promise = axios(Parameter);
promise.then(function(result) {
    renderCity(result.data);
});

function renderCity(data) {
    for (const province of data) {
        citis.options[citis.options.length] = new Option(province.Name, province.Name);
    }
    citis.onchange = function() {
        districts.length = 1;
        wards.length = 1;
        if (this.value != "") {
            const selectedProvince = data.find(province => province.Name === this.value);

            for (const district of selectedProvince.Districts) {
                districts.options[districts.options.length] = new Option(district.Name, district.Name);
            }
        }
    };
    districts.onchange = function() {
        wards.length = 1;
        const selectedProvince = data.find(province => province.Name === citis.value);
        if (this.value != "") {
            const selectedDistrict = selectedProvince.Districts.find(district => district.Name === this.value);

            for (const ward of selectedDistrict.Wards) {
                wards.options[wards.options.length] = new Option(ward.Name, ward.Name);
            }
        }
    };
}