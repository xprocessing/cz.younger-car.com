// 立即执行函数包裹所有代码，隔离作用域
(function(window, document) {
    // 防止重复初始化的全局唯一标识
    if (window.lxInventoryCheck) return;
    window.lxInventoryCheck = true;

    // ========== 内部变量/函数（均为局部，不污染全局） ==========
    // 获取company_id的Cookie值
    const companyId = document.cookie.match(/(^|;)\s*company_id=([^;]+)/)?.[2];

    // 验证不通过则终止执行
    if (companyId !== '901571037510692352') throw new Error('公司company_id非法，终止执行');

    // ========== 工具函数（局部） ==========
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
        return str.replace(/\\/g, '\\\\')
            .replace(/"/g, '\\"')
            .replace(/'/g, "\\'")
            .replace(/\//g, '\\/');
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

    // ========== 请求配置（局部） ==========
    const getRequestHeaders = () => {
        const auth_token = getAuthToken();
        const original_auth_token =  decodeURIComponent(auth_token);
        const request_id = generateRequestId();

        return {
            "accept": "application/json, text/plain, */*",
            "accept-encoding": "gzip, deflate, br, zstd",
            "accept-language": "zh-CN,zh;q=0.9",
            "ak-client-type": "web",
            "ak-origin": "https://erp.lingxing.com",
            "auth-token": original_auth_token,
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
            "x-ak-platform": "2",
            "x-ak-request-id": request_id,
            "x-ak-request-source": "erp",
            "x-ak-uid": "10956565",
            "x-ak-version": "3.7.3.3.0.027",
            "x-ak-zid": "10796914"
        };
    };

    // ========== 库存查询功能（局部） ==========
    // 显示库存信息弹窗
    const showInventoryTable = (dataList, skuValue) => {
        if (document.querySelector('.lx-inventory-modal')) return;

        const sortedList = dataList.sort((a, b) => b.average_age - a.average_age);

        const modal = document.createElement('div');
        modal.className = 'lx-inventory-modal';
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

        const title = document.createElement('h3');
        title.textContent = `SKU【${skuValue}】全仓库存信息（按平均库龄降序）`;
        title.style.margin = '0 0 15px 0';
        title.style.color = '#333';

        const table = document.createElement('table');
        table.style.width = '100%';
        table.style.borderCollapse = 'collapse';
        table.style.minWidth = '600px';

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

        const overlay = document.createElement('div');
        overlay.className = 'lx-inventory-overlay';
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

        table.appendChild(thead);
        table.appendChild(tbody);
        modal.appendChild(title);
        modal.appendChild(table);
        modal.appendChild(closeBtn);

        document.body.appendChild(overlay);
        document.body.appendChild(modal);
    };

    // 第一步：获取productId
    async function sendProductIdRequest(searchValue) {
        const requestProductIdUrl = "https://gw.lingxingerp.com/universal/storage/api/universal/summaryForsku";

        const requestProductIdBody = {
            "attributes": [],
            "principalList": [],
            "productUids": [], 
            "searchField": "sku",
            "searchValue": searchValue,
            "pageNo": 1, 
            "pageSize": 20,
            "seniorSearchList": [],
            "req_time_sequence": "/universal/storage/api/universal/summaryForsku$$3"
        };

        const response = await fetch(requestProductIdUrl, {
            method: "POST",
            headers: getRequestHeaders(),
            body: JSON.stringify(requestProductIdBody),
            credentials: "include",
            mode: "cors"
        });

        if (!response.ok) {
            let errorData;
            try {
                errorData = await response.json();
            } catch (e) {
                errorData = await response.text();
            }
            throw new Error(`获取productId失败 [${response.status}]：${JSON.stringify(errorData)}`);
        }

        const data = await response.json();
        if (!data.data?.data?.[0]?.productId) {
            throw new Error(`未找到SKU【${searchValue}】对应的productId`);
        }
        return data.data.data[0].productId;
    }

    // 第二步：查询库存明细
    async function sendSkuDetailRequest(productId, skuValue) {
        const requestUrl = "https://gw.lingxingerp.com/universal/storage/api/universal/summaryForsku/detail";
        
        const requestBody = {
            productId: productId,
            firstId: "1995641607401836545",
            hasChildrenRow: false,
            attributes: [],
            principalList: [],
            productUids: [],
            searchField: "sku",
            searchValue: skuValue,
            pageNo: 1,
            pageSize: 20,
            seniorSearchList: [],
            req_time_sequence: "/universal/storage/api/universal/summaryForsku/detail$$3"
        };

        const response = await fetch(requestUrl, {
            method: "POST",
            headers: getRequestHeaders(),
            body: JSON.stringify(requestBody),
            credentials: "include",
            mode: "cors"
        });

        if (!response.ok) {
            let errorData;
            try {
                errorData = await response.json();
            } catch (e) {
                errorData = await response.text();
            }
            throw new Error(`查询库存明细失败 [${response.status}]：${JSON.stringify(errorData)}`);
        }

        const data = await response.json();
        return data;
    }

    // 整合两个请求的库存查询函数（防抖处理）
    const fetchInventory = debounce(async (skuValue) => {
        try {
            if (document.querySelector('.lx-inventory-modal')) return;

            // 第一步：获取productId
            const productId = await sendProductIdRequest(skuValue);
            console.log(`获取到SKU【${skuValue}】的productId：`, productId);

            // 第二步：查询库存明细
            const result = await sendSkuDetailRequest(productId, skuValue);
            console.log(`SKU【${skuValue}】的库存明细：`, result);

            // 假设返回的数据结构需要转换以适配showInventoryTable
            // 根据实际接口返回调整数据转换逻辑
            if (result.data && result.data.childrens && result.data.childrens.length > 0) {
                // 这里可能需要根据实际返回结构进行数据转换
                const inventoryList = result.data.childrens.map(item => ({
                    wh_name: item.wh_name || item.warehouseName,
                    total: item.totalNum || item.stockQuantity,
                    pending_num: item.transportingNum || item.pendingQuantity,
                    average_age: Math.round(
  (30*(item.from31To60DayNum||0)+60*(item.from61To90DayNum||0)+90*(item.from91To180DayNum||0)+180*(item.from181To270DayNum||0)+270*(item.from271To330DayNum||0)+330*(item.from331To365DayNum||0)+365*(item.from365PlusDayNum||0)) / (item.totalNum || 1) // 分母用1避免除以0
) || 0

                }));
                
                showInventoryTable(inventoryList, skuValue);
            } else {
                alert(`未找到SKU【${skuValue}】的库存数据`);
            }

        } catch (error) {
            console.error('库存查询失败:', error);
            alert(`查询失败：${error.message}\n请检查网络或权限`);
        }
    });

    // ========== DOM监控（局部） ==========
    // 处理符合条件的文本节点
    const handleTextNode = (node) => {
        if (node.nodeType !== 3 || !/-.+-/.test(node.textContent) || node.parentElement.dataset.lxH) return;
        const span = document.createElement('span');
        span.dataset.lxH = 1;
        span.style.cursor = 'help';
        //鼠标滑过背景颜色变绿
        span.style.transition = 'background-color 0.3s';
        span.onmouseenter = () => { span.style.backgroundColor = '#d4edda'; };
        span.onmouseleave = () => { span.style.backgroundColor = 'transparent'; };
        
        span.onmouseover = () => fetchInventory(node.textContent.trim());
        
        node.parentElement.replaceChild(span, node);
        span.appendChild(node);
    };

    // 遍历DOM节点
    const traverseDOM = (el) => {
        el.childNodes.forEach(child => {
            if (child.tagName && ['SCRIPT', 'STYLE'].includes(child.tagName)) return;
            child.nodeType === 1 ? traverseDOM(child) : handleTextNode(child);
        });
    };

    // 监听动态DOM变化
    new MutationObserver((muts) => muts.forEach((mut) => mut.addedNodes.forEach(traverseDOM)))
        .observe(document.body, { childList: true, subtree: true });

    // 初始化处理现有DOM
    traverseDOM(document.body);

})(window, document);