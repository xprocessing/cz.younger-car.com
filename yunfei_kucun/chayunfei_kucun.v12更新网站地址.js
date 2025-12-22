// 获取company_id的Cookie值（一行简化）
const companyId = document.cookie.match(/(^|;)\s*company_id=([^;]+)/)?.[2];

// 验证不通过则终止执行
if (companyId !== '901571037510692352') throw new Error('公司company_id非法，终止执行');

// 验证通过后的代码
console.log('验证通过，继续执行');
// ...后续逻辑
// ========== 工具函数 ==========
// 获取cookie中auth-token的值
const getAuthToken = () => (document.cookie.match(/auth-token=([^;]+)/) || [])[1] || null;

// 生成UUID作为x-ak-request-id
const generateRequestId = () => {
    return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function (c) {
        const r = Math.random() * 16 | 0;
        const v = c === 'x' ? r : (r & 0x3 | 0x8);
        return v.toString(16);
    });
};

// 单斜杠转义处理函数
const escapeSlashes = (str) => {
    return str.replace(/\\/g, '\\\\')  // 转义反斜杠
        .replace(/"/g, '\\"')    // 转义双引号
        .replace(/'/g, "\\'")    // 转义单引号
        .replace(/\//g, '\\/');  // 转义正斜杠
};

// 防抖函数（防止重复请求）
const debounce = (func, delay = 300) => {
    let timer = null;
    return function (...args) {
        clearTimeout(timer);
        timer = setTimeout(() => {
            func.apply(this, args);
        }, delay);
    };
};

// ========== 请求配置 ==========
// 获取最新的请求头配置
const getRequestHeaders = () => {
    const auth_token = getAuthToken();
    const request_id = generateRequestId();

    return {
        "accept": "application/json, text/plain, */*",
        "accept-encoding": "gzip, deflate, br, zstd",
        "accept-language": "zh-CN,zh;q=0.9,en;q=0.8,en-GB;q=0.7,en-US;q=0.6",
        "ak-client-type": "web",
        "ak-origin": "https://erp.lingxing.com",
        "auth-token": auth_token,
        "content-type": "application/json;charset=UTF-8",
        "origin": "https://erp.lingxing.com",
        "priority": "u=1, i",
        "referer": "https://erp.lingxing.com/",
        "sec-ch-ua": "\"Chromium\";v=\"135\", \"Not-A.Brand\";v=\"8\"",
        "sec-ch-ua-mobile": "?0",
        "sec-ch-ua-platform": "\"Windows\"",
        "sec-fetch-dest": "empty",
        "sec-fetch-mode": "cors",
        "sec-fetch-site": "cross-site",
        "sec-fetch-storage-access": "active",
        "user-agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36",
        "x-ak-company-id": "901571037510692352",
        "x-ak-env-key": "SAAS-126",
        "x-ak-language": "zh",
        "x-ak-platform": "1",
        "x-ak-request-id": request_id,
        "x-ak-request-source": "erp",
        "x-ak-uid": "10956565",
        "x-ak-version": "3.7.2.3.0.095",
        "x-ak-zid": "10796914"
    };
};

// ========== 订单运费查询功能 ==========
function getOrderInfo(global_order_no) {
    const header_data = getRequestHeaders();

    const url = `https://erp.lingxing.com/api/platforms/oms/order_list/detail?global_order_no=${global_order_no}&req_time_sequence=%2Fapi%2Fplatforms%2Foms%2Forder_list%2Fdetail$$1`;

    fetch(url, {
        method: 'GET',
        headers: header_data,
        credentials: 'include'
    })
        .then(res => {
            if (!res.ok) throw new Error(`HTTP错误：${res.status}`);
            return res.json();
        })
        .then(data => {
            console.log(data);
            console.log("估算计费重:" + data.data.logistics_info.pre_fee_weight);
            console.log("估算尺寸:" + data.data.logistics_info.pre_package_size);
            console.log("买家国家代码:" + data.data.receive_info.receiver_country_code);
            console.log("买家邮政编码:" + data.data.receive_info.postal_code);

            const pre_fee_weight = data.data.logistics_info.pre_fee_weight;
            const pre_package_size = data.data.logistics_info.pre_package_size;
            const receiver_country_code = data.data.receive_info.receiver_country_code;
            const postal_code = data.data.receive_info.postal_code;
            const city = data.data.receive_info.city;

            // 解析包裹尺寸
            const [length, width, heightStr] = pre_package_size.split('x');
            const height = heightStr.replace('cm', '');

            // 转换重量单位（g -> kg）
            let weight_kg = parseFloat(pre_fee_weight.replace(' g', '')) / 1000;

            // 构建运费查询URL并打开新窗口
            const url2 = `https://cz.younger-car.com/yunfei_kucun/chayunfei.php?global_order_no=${global_order_no}&receiver_country_code=${receiver_country_code}&postcode=${postal_code}&weight=${weight_kg}&length=${length}&width=${width}&height=${height}&city=${encodeURIComponent(city)}`;
            console.log(url2);
            window.open(url2, '_blank');
        })
        .catch(err => console.error('请求失败：', err.message));
}

// ========== 极速订单运费查询功能（新增） ==========
function getFastOrderInfo(global_order_no) {
    // 构建极速运费查询URL并打开新窗口
    const fastUrl = `https://cz.younger-car.com/yunfei_kucun/chayunfei_fast.php?global_order_no=${global_order_no}`;
    console.log('极速运费查询URL:', fastUrl);
    window.open(fastUrl, '_blank');
}

// ========== 库存查询功能 ==========
// 格式化展示数据（表格形式）

// 显示库存信息弹窗
const showInventoryTable = (dataList, skuValue) => {
    // 先检查是否已有弹窗，避免重复创建
    if (document.querySelector('.inventory-modal')) return;

    const sortedList = dataList.sort((a, b) => b.average_age - a.average_age);

    // 创建弹窗元素
    const modal = document.createElement('div');
    modal.className = 'inventory-modal';
    modal.style.cssText = `
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background-color: white;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
        z-index: 9999;
        max-height: 80vh;
        overflow: auto;
    `;

    // 创建标题（添加SKU显示）
    const title = document.createElement('h3');
    title.textContent = `SKU【${skuValue}】库存信息（按平均库龄降序）`;
    title.style.margin = '0 0 15px 0';
    title.style.color = '#333';

    // 创建表格
    const table = document.createElement('table');
    table.style.width = '100%';
    table.style.borderCollapse = 'collapse';
    table.style.minWidth = '600px'; // 增加最小宽度以适应新增列

    // 表头
    const thead = document.createElement('thead');
    thead.innerHTML = `
        <tr style="background-color: #f5f7fa;">
            <th style="padding: 10px; text-align: center; border: 1px solid #e6e6e6; font-weight: 600;">序号</th>
            <th style="padding: 10px; text-align: left; border: 1px solid #e6e6e6; font-weight: 600;">仓库名称</th>
            <th style="padding: 10px; text-align: center; border: 1px solid #e6e6e6; font-weight: 600;">库存总量</th>
            <th style="padding: 10px; text-align: center; border: 1px solid #e6e6e6; font-weight: 600;">待到货</th>
            <th style="padding: 10px; text-align: center; border: 1px solid #e6e6e6; font-weight: 600;">平均库龄</th>
        </tr>
    `;

    // 表格内容
    const tbody = document.createElement('tbody');
    sortedList.forEach((item, index) => {
        const row = document.createElement('tr');
        row.style.backgroundColor = index % 2 === 0 ? '#fff' : '#fafafa';

        row.innerHTML = `
            <td style="padding: 8px 10px; text-align: center; border: 1px solid #e6e6e6;">${index + 1}</td>
            <td style="padding: 8px 10px; text-align: left; border: 1px solid #e6e6e6;">${item.wh_name || '未知'}</td>
            <td style="padding: 8px 10px; text-align: center; border: 1px solid #e6e6e6;">${item.total || 0}</td>
            <td style="padding: 8px 10px; text-align: center; border: 1px solid #e6e6e6;">${item.pending_num || 0}</td>
            <td style="padding: 8px 10px; text-align: center; border: 1px solid #e6e6e6;">${item.average_age || 0}天</td>
        `;

        tbody.appendChild(row);
    });

    // 创建关闭按钮
    const closeBtn = document.createElement('button');
    closeBtn.textContent = '关闭';
    closeBtn.style.cssText = `
        margin-top: 15px;
        padding: 8px 16px;
        background-color: #409eff;
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 14px;
    `;
    closeBtn.onclick = () => {
        document.body.removeChild(modal);
        document.body.removeChild(overlay);
    };

    // 创建遮罩层
    const overlay = document.createElement('div');
    overlay.className = 'inventory-overlay';
    overlay.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        z-index: 9998;
    `;
    overlay.onclick = closeBtn.onclick;

    // 组装弹窗
    table.appendChild(thead);
    table.appendChild(tbody);
    modal.appendChild(title);
    modal.appendChild(table);
    modal.appendChild(closeBtn);

    // 添加到页面
    document.body.appendChild(overlay);
    document.body.appendChild(modal);
};

// 发送库存查询请求（防抖处理）
const fetchInventory = debounce(async (skuValue) => {
    try {
        // 检查是否已有弹窗，有则直接返回
        if (document.querySelector('.inventory-modal')) return;

        const escapedSku = escapeSlashes(skuValue);
        const seniorSearchList = `[{\"name\":\"SKU\",\"search_field\":\"sku\",\"search_value\":[\"${escapedSku}\"]}]`;

        const requestData = {
            "wid_list": "",
            "mid_list": "",
            "sid_list": "",
            "cid_list": "",
            "bid_list": "",
            "principal_list": "",
            "product_type_list": "",
            "product_attribute": "",
            "product_status": "",
            "search_field": "sku",
            "search_value": skuValue,
            "is_sku_merge_show": 0,
            "is_hide_zero_stock": 0,
            "offset": 0,
            "length": 20,
            "sort_field": "",
            "sort_type": "",
            "gtag_ids": "",
            "senior_search_list": seniorSearchList,
            "permission_uid_list": "",
            "country_code_list": "",
            "req_time_sequence": "/api/storage/lists$$7"
        };

        console.log('请求参数:', requestData);

        const response = await fetch('https://erp.lingxing.com/api/storage/lists', {
            method: 'POST',
            headers: getRequestHeaders(),
            body: JSON.stringify(requestData),
            credentials: 'include'
        });

        if (!response.ok) {
            throw new Error(`请求失败：${response.status}`);
        }

        const result = await response.json();

        if (result.data && result.data.list && result.data.list.length > 0) {
            showInventoryTable(result.data.list, skuValue); // 传入skuValue参数
        } else {
            alert(`未找到SKU【${skuValue}】的库存数据`);
        }

    } catch (error) {
        console.error('库存查询失败:', error);
        alert(`查询失败：${error.message}\n请检查网络或权限`);
    }
});

// ========== 页面元素初始化 ==========
// 统一的添加/更新运费按钮函数
function ensureFreightButton(row) {
    if (!row || !row.classList.contains('special-row')) return;

    const global_order_no = row.getAttribute('rowid');
    if (!global_order_no) return;

    const left = row.querySelector('.left');
    if (!left) return;

    // 处理「查看运费」按钮
    let freightBtn = left.querySelector('button[data-freight-btn]');
    if (!freightBtn) {
        freightBtn = document.createElement('button');
        freightBtn.innerText = '查看运费';
        freightBtn.setAttribute('data-freight-btn', '');
        freightBtn.style.marginLeft = '8px';
        freightBtn.style.padding = '4px 8px';
        freightBtn.style.cursor = 'pointer';
        freightBtn.style.backgroundColor = '#67c23a';
        freightBtn.style.color = 'white';
        freightBtn.style.border = 'none';
        freightBtn.style.borderRadius = '4px';
        freightBtn.style.fontSize = '12px';
        left.appendChild(freightBtn);
    }
    // 绑定查看运费按钮点击事件
    freightBtn.onclick = function (e) {
        e.stopPropagation();
        getOrderInfo(global_order_no);
    };

    // 处理「极速运费」按钮（新增）
    let fastFreightBtn = left.querySelector('button[data-fast-freight-btn]');
    if (!fastFreightBtn) {
        fastFreightBtn = document.createElement('button');
        fastFreightBtn.innerText = '极速运费';
        fastFreightBtn.setAttribute('data-fast-freight-btn', '');
        fastFreightBtn.style.marginLeft = '8px';
        fastFreightBtn.style.padding = '4px 8px';
        fastFreightBtn.style.cursor = 'pointer';
        fastFreightBtn.style.backgroundColor = '#e6a23c'; // 橙色区分样式
        fastFreightBtn.style.color = 'white';
        fastFreightBtn.style.border = 'none';
        fastFreightBtn.style.borderRadius = '4px';
        fastFreightBtn.style.fontSize = '12px';
        left.appendChild(fastFreightBtn);
    }
    // 绑定极速运费按钮点击事件
    fastFreightBtn.onclick = function (e) {
        e.stopPropagation();
        getFastOrderInfo(global_order_no);
    };
}

// 统一的添加/更新库存按钮函数
function ensureInventoryButton(tr) {
    if (!tr || !tr.hasAttribute('rowid')) return;

    // 查找SKU值
    const skuSpans = tr.querySelectorAll('span');
    let skuValue = null;
    let targetSpan = null;

    for (const span of skuSpans) {
        if (span.textContent.includes('SKU')) {
            const nextSpan = span.nextElementSibling;
            if (nextSpan && nextSpan.tagName === 'SPAN') {
                targetSpan = nextSpan.querySelector('div > span');
                if (targetSpan) {
                    skuValue = targetSpan.textContent.trim();
                }
            }
            break;
        }
    }

    if (!skuValue) return;

    // 找到前一个tr的left元素
    const prevTr = tr.previousElementSibling;
    if (!prevTr) return;

    const leftElement = prevTr.querySelector('.left');
    if (!leftElement) return;

    // 检查是否已有库存按钮
    let inventoryBtn = leftElement.querySelector('.inventory-btn');
    if (!inventoryBtn) {
        inventoryBtn = document.createElement('button');
        inventoryBtn.className = 'inventory-btn';
        inventoryBtn.textContent = '查看库存';
        inventoryBtn.style.cssText = `
            margin-left: 8px;
            padding: 4px 8px;
            cursor: pointer;
            background-color: #409eff;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 12px;
            transition: all 0.2s ease;
        `;

        // 点击事件
        inventoryBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            fetchInventory(skuValue);
        });

        // 鼠标悬停事件
        inventoryBtn.addEventListener('mouseenter', function (e) {
            e.stopPropagation();
            fetchInventory(skuValue);
            inventoryBtn.style.backgroundColor = '#2d8cf0';
            inventoryBtn.style.transform = 'scale(1.05)';
        });

        // 鼠标离开事件
        inventoryBtn.addEventListener('mouseleave', function () {
            inventoryBtn.style.backgroundColor = '#409eff';
            inventoryBtn.style.transform = 'scale(1)';
        });

        leftElement.appendChild(inventoryBtn);
    }
    if (inventoryBtn) {
        // 点击事件
        inventoryBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            fetchInventory(skuValue);
        });

        // 鼠标悬停事件
        inventoryBtn.addEventListener('mouseenter', function (e) {
            e.stopPropagation();
            fetchInventory(skuValue);
            inventoryBtn.style.backgroundColor = '#2d8cf0';
            inventoryBtn.style.transform = 'scale(1.05)';
        });

        // 鼠标离开事件
        inventoryBtn.addEventListener('mouseleave', function () {
            inventoryBtn.style.backgroundColor = '#409eff';
            inventoryBtn.style.transform = 'scale(1)';
        });
    }
}

// 初始加载时处理所有库存按钮
document.querySelectorAll('tr[rowid]').forEach(tr => {
    ensureInventoryButton(tr);
});

// ========== 初始化执行 ==========
// 初始加载时处理所有已存在的.special-row
document.querySelectorAll('.special-row').forEach(row => {
    ensureFreightButton(row);
});

// 鼠标进入时确保运费按钮存在
document.addEventListener('mouseenter', function (e) {
    // 检查是否是运费按钮所在行
    const freightRow = e.target.closest('.special-row');
    if (freightRow) {
        ensureFreightButton(freightRow);
    }

    // 检查是否是库存按钮所在行
    const inventoryRow = e.target.closest('tr[rowid]');
    if (inventoryRow) {
        ensureInventoryButton(inventoryRow);
    }
}, true);

// 监听DOM变化，自动重新处理按钮
const observer = new MutationObserver((mutations) => {
    let shouldProcess = false;
    mutations.forEach(mutation => {
        // 子节点增减、属性变化、文本内容变化都触发处理
        if (mutation.addedNodes.length || mutation.removedNodes.length ||
            mutation.type === 'attributes' || mutation.type === 'characterData') {
            shouldProcess = true;
        }
    });
    if (shouldProcess) {
        // 处理运费按钮
        document.querySelectorAll('tr[rowid]').forEach(tr => {
            ensureInventoryButton(tr);
        });

        // 处理库存按钮
        document.querySelectorAll('.special-row').forEach(row => {
            ensureFreightButton(row);
        });
    }
});

// 配置观察器
observer.observe(document.body, {
    childList: true,
    subtree: true,
    attributes: true,
    characterData: true
});