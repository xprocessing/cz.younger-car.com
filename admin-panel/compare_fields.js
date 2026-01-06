const fs = require('fs');
const path = require('path');

// 读取文件内容
const filePath = 'c:\\Users\\hzf16\\Desktop\\cz.younger-car.com\\admin-panel\\views\\inventory_details_fba\\index.php';
const content = fs.readFileSync(filePath, 'utf8');

// 提取表格中的data-field属性
const dataFieldRegex = /data-field="([^"]+)"/g;
const tableFields = new Set();
let match;
while ((match = dataFieldRegex.exec(content)) !== null) {
    tableFields.add(match[1]);
}

// 提取模态框中的复选框value值
const checkboxValueRegex = /<input class="form-check-input field-checkbox" type="checkbox" value="([^"]+)"/g;
const modalFields = new Set();
while ((match = checkboxValueRegex.exec(content)) !== null) {
    modalFields.add(match[1]);
}

// 找出表格中有但模态框中没有的字段
const missingInModal = Array.from(tableFields).filter(field => !modalFields.has(field));

// 找出模态框中有但表格中没有的字段
const missingInTable = Array.from(modalFields).filter(field => !tableFields.has(field));

// 输出结果
console.log('=== 字段选择与展示一致性检查结果 ===');
console.log(`\n表格中共有 ${tableFields.size} 个字段`);
console.log(`模态框中共有 ${modalFields.size} 个复选框`);

if (missingInModal.length > 0) {
    console.log(`\n❌ 表格中有但模态框中没有的字段 (${missingInModal.length}个):`);
    missingInModal.forEach(field => console.log(`  - ${field}`));
} else {
    console.log('\n✅ 所有表格字段在模态框中都有对应的复选框');
}

if (missingInTable.length > 0) {
    console.log(`\n❌ 模态框中有但表格中没有的字段 (${missingInTable.length}个):`);
    missingInTable.forEach(field => console.log(`  - ${field}`));
} else {
    console.log('\n✅ 所有模态框复选框在表格中都有对应的字段');
}

if (missingInModal.length === 0 && missingInTable.length === 0) {
    console.log('\n🎉 字段选择与展示完全一致！');
}