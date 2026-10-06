const fs = require('fs');
const path = require('path');

const file = path.join(__dirname, '../FE_JPM/src/views/client/customer/TaiKhoan.vue');
let content = fs.readFileSync(file, 'utf8');

const regex = /onMutate: async \(newAddress\) => \{[\s\S]*?return \{ previousAddresses \};\s*\},/m;
content = content.replace(regex, '');

const regexOnError = /onError: \(err, newAddress, context\) => \{[\s\S]*?toaster\.error\("Có lỗi xảy ra!"\);\s*\},/m;
content = content.replace(regexOnError, 'onError: (error) => { if (error.response?.status === 422) { Object.values(error.response.data.errors).forEach(err => toaster.error(err[0])); } else { toaster.error("Có lỗi xảy ra khi lưu địa chỉ!"); } },');

fs.writeFileSync(file, content, 'utf8');
console.log('Fixed TaiKhoan.vue');
