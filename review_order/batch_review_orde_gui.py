import review_order_func as review_order
import time
import json
import tkinter as tk
from tkinter import ttk, scrolledtext, messagebox
import threading
import queue
import sys
from datetime import datetime

# 汇率配置：美元转人民币
USD_TO_CNY_RATE = 7.0

class LogRedirector:
    """
    日志重定向类，将print输出重定向到GUI界面
    """
    def __init__(self, text_widget):
        self.text_widget = text_widget
        self.old_stdout = sys.stdout
        self.queue = queue.Queue()
        self.running = True
        
    def write(self, text):
        self.queue.put(text)
        
    def flush(self):
        pass
    
    def start(self):
        """
        开始处理队列中的日志消息
        """
        def process_queue():
            while self.running:
                try:
                    text = self.queue.get(block=False)
                    if text:
                        self.text_widget.insert(tk.END, text)
                        self.text_widget.see(tk.END)
                except queue.Empty:
                    time.sleep(0.1)
        
        thread = threading.Thread(target=process_queue, daemon=True)
        thread.start()
    
    def stop(self):
        """
        停止日志重定向
        """
        self.running = False
        sys.stdout = self.old_stdout

class BatchReviewGUI:
    """
    批量审核订单图形界面
    """
    def __init__(self, root):
        self.root = root
        self.root.title("批量审核订单系统")
        self.root.geometry("900x700")
        self.root.resizable(True, True)
        
        # 设置主题
        self.style = ttk.Style()
        self.style.theme_use('clam')
        
        # 配置样式
        self.style.configure('TButton', font=('微软雅黑', 10), padding=6)
        self.style.configure('TLabel', font=('微软雅黑', 10))
        self.style.configure('TProgressbar', thickness=20)
        
        # 创建主框架
        self.main_frame = ttk.Frame(self.root, padding="10")
        self.main_frame.pack(fill=tk.BOTH, expand=True)
        
        # 顶部控制区
        self.control_frame = ttk.LabelFrame(self.main_frame, text="控制中心", padding="10")
        self.control_frame.pack(fill=tk.X, pady=5)
        
        # 启动按钮
        self.start_button = ttk.Button(self.control_frame, text="开始批量审核", command=self.start_process)
        self.start_button.pack(side=tk.LEFT, padx=5)
        
        # 停止按钮
        self.stop_button = ttk.Button(self.control_frame, text="停止处理", command=self.stop_process, state=tk.DISABLED)
        self.stop_button.pack(side=tk.LEFT, padx=5)
        
        # 状态标签
        self.status_var = tk.StringVar(value="就绪")
        self.status_label = ttk.Label(self.control_frame, textvariable=self.status_var, font=('微软雅黑', 10, 'bold'))
        self.status_label.pack(side=tk.RIGHT, padx=10)
        
        # 进度条区域
        self.progress_frame = ttk.LabelFrame(self.main_frame, text="处理进度", padding="10")
        self.progress_frame.pack(fill=tk.X, pady=5)
        
        # 总进度条
        self.total_progress_var = tk.DoubleVar(value=0)
        self.total_progress = ttk.Progressbar(self.progress_frame, variable=self.total_progress_var, maximum=100)
        self.total_progress.pack(fill=tk.X, pady=5)
        
        # 进度信息
        self.progress_info_var = tk.StringVar(value="等待开始")
        self.progress_info = ttk.Label(self.progress_frame, textvariable=self.progress_info_var)
        self.progress_info.pack(fill=tk.X)
        
        # 日志区域
        self.log_frame = ttk.LabelFrame(self.main_frame, text="处理日志", padding="10")
        self.log_frame.pack(fill=tk.BOTH, expand=True, pady=5)
        
        self.log_text = scrolledtext.ScrolledText(self.log_frame, width=100, height=20, font=('Consolas', 9))
        self.log_text.pack(fill=tk.BOTH, expand=True)
        self.log_text.config(state=tk.DISABLED)
        
        # 结果展示区域
        self.result_frame = ttk.LabelFrame(self.main_frame, text="处理结果", padding="10")
        self.result_frame.pack(fill=tk.BOTH, expand=True, pady=5)
        
        # 结果统计
        self.stats_frame = ttk.Frame(self.result_frame)
        self.stats_frame.pack(fill=tk.X, pady=5)
        
        self.total_orders_var = tk.StringVar(value="总订单数: 0")
        self.success_orders_var = tk.StringVar(value="成功数: 0")
        self.failed_orders_var = tk.StringVar(value="失败数: 0")
        
        ttk.Label(self.stats_frame, textvariable=self.total_orders_var).pack(side=tk.LEFT, padx=10)
        ttk.Label(self.stats_frame, textvariable=self.success_orders_var).pack(side=tk.LEFT, padx=10)
        ttk.Label(self.stats_frame, textvariable=self.failed_orders_var).pack(side=tk.LEFT, padx=10)
        
        # 结果表格
        self.tree_frame = ttk.Frame(self.result_frame)
        self.tree_frame.pack(fill=tk.BOTH, expand=True)
        
        self.result_tree = ttk.Treeview(self.tree_frame, columns=(
            "order_no", "status", "fee", "type_id", "wid", "channel", "message"
        ), show="headings")
        
        # 配置列
        self.result_tree.heading("order_no", text="订单号")
        self.result_tree.heading("status", text="状态")
        self.result_tree.heading("fee", text="最优运费(元)")
        self.result_tree.heading("type_id", text="物流类型ID")
        self.result_tree.heading("wid", text="仓库ID")
        self.result_tree.heading("channel", text="渠道编码")
        self.result_tree.heading("message", text="消息")
        
        # 设置列宽
        self.result_tree.column("order_no", width=150)
        self.result_tree.column("status", width=80)
        self.result_tree.column("fee", width=100)
        self.result_tree.column("type_id", width=120)
        self.result_tree.column("wid", width=80)
        self.result_tree.column("channel", width=120)
        self.result_tree.column("message", width=200)
        
        # 添加滚动条
        scrollbar = ttk.Scrollbar(self.tree_frame, orient=tk.VERTICAL, command=self.result_tree.yview)
        self.result_tree.configure(yscroll=scrollbar.set)
        scrollbar.pack(side=tk.RIGHT, fill=tk.Y)
        self.result_tree.pack(fill=tk.BOTH, expand=True)
        
        # 初始化变量
        self.process_thread = None
        self.stop_event = threading.Event()
        self.process_results = []
        self.log_redirector = None
        
    def start_process(self):
        """
        开始处理订单
        """
        # 重置状态
        self.stop_event.clear()
        self.process_results = []
        self.total_progress_var.set(0)
        self.progress_info_var.set("初始化中...")
        self.status_var.set("处理中")
        
        # 清空日志和结果
        self.log_text.config(state=tk.NORMAL)
        self.log_text.delete(1.0, tk.END)
        self.log_text.config(state=tk.DISABLED)
        
        for item in self.result_tree.get_children():
            self.result_tree.delete(item)
        
        # 禁用开始按钮，启用停止按钮
        self.start_button.config(state=tk.DISABLED)
        self.stop_button.config(state=tk.NORMAL)
        
        # 重定向日志
        self.log_text.config(state=tk.NORMAL)
        self.log_redirector = LogRedirector(self.log_text)
        sys.stdout = self.log_redirector
        self.log_redirector.start()
        
        # 启动处理线程
        self.process_thread = threading.Thread(target=self.process_orders)
        self.process_thread.daemon = True
        self.process_thread.start()
        
        # 开始监控线程
        monitor_thread = threading.Thread(target=self.monitor_process)
        monitor_thread.daemon = True
        monitor_thread.start()
    
    def stop_process(self):
        """
        停止处理订单
        """
        self.stop_event.set()
        self.status_var.set("停止中...")
    
    def process_orders(self):
        """
        处理订单的核心逻辑
        """
        try:
            print(f"[{datetime.now().strftime('%Y-%m-%d %H:%M:%S')}] 开始批量审核订单...")
            
            # 1. 初始化基础数据
            print(f"[{datetime.now().strftime('%Y-%m-%d %H:%M:%S')}] 获取店铺列表...")
            store_list = review_order.get_store_list()
            print(f"[{datetime.now().strftime('%Y-%m-%d %H:%M:%S')}] 获取到 {len(store_list)} 个店铺")
            
            if not store_list:
                print(f"[{datetime.now().strftime('%Y-%m-%d %H:%M:%S')}] 未获取到店铺列表")
                return
            
            print(f"[{datetime.now().strftime('%Y-%m-%d %H:%M:%S')}] 获取物流渠道列表...")
            logistics_list = review_order.get_logistics_list()
            print(f"[{datetime.now().strftime('%Y-%m-%d %H:%M:%S')}] 获取到 {len(logistics_list)} 个物流渠道")
            
            if not logistics_list:
                print(f"[{datetime.now().strftime('%Y-%m-%d %H:%M:%S')}] 未获取到物流渠道列表")
                return
            
            print(f"[{datetime.now().strftime('%Y-%m-%d %H:%M:%S')}] 获取订单列表...")
            orders_list = review_order.get_orders_list()
            
            # 筛选处理wid=0的订单，测试阶段取前5个
            target_orders = [order for order in orders_list if order.get("wid") == "0"][:5]
            print(f"[{datetime.now().strftime('%Y-%m-%d %H:%M:%S')}] 筛选出 {len(target_orders)} 个待处理订单")
            
            if not target_orders:
                print(f"[{datetime.now().strftime('%Y-%m-%d %H:%M:%S')}] 无待处理订单")
                return
            
            total_orders = len(target_orders)
            
            # 2. 遍历处理每个订单
            for idx, order in enumerate(target_orders, 1):
                if self.stop_event.is_set():
                    print(f"[{datetime.now().strftime('%Y-%m-%d %H:%M:%S')}] 处理被用户停止")
                    break
                
                # 更新进度
                progress = (idx / total_orders) * 100
                self.total_progress_var.set(progress)
                self.progress_info_var.set(f"处理第 {idx}/{total_orders} 个订单")
                
                print(f"\n[{datetime.now().strftime('%Y-%m-%d %H:%M:%S')}] === 开始处理第 {idx} 个订单 ===")
                
                try:
                    order_result = self.process_single_order(order, store_list, logistics_list)
                    self.process_results.append(order_result)
                except Exception as e:
                    error_msg = f"订单 {order.get('global_order_no')} 处理异常：{str(e)}"
                    print(f"[{datetime.now().strftime('%Y-%m-%d %H:%M:%S')}] {error_msg}")
                    self.process_results.append({
                        "global_order_no": order.get("global_order_no"),
                        "status": "failed",
                        "message": error_msg,
                        "final_fee": 0,
                        "final_type_id": "",
                        "final_wid": "",
                        "final_channel_code": ""
                    })
                
                print(f"[{datetime.now().strftime('%Y-%m-%d %H:%M:%S')}] === 结束处理第 {idx} 个订单 ===")
                
            # 完成处理
            if not self.stop_event.is_set():
                print(f"\n[{datetime.now().strftime('%Y-%m-%d %H:%M:%S')}] 批量处理完成！")
                self.total_progress_var.set(100)
                self.progress_info_var.set("处理完成")
                self.status_var.set("完成")
            else:
                self.status_var.set("已停止")
                self.progress_info_var.set("处理已停止")
                
        except Exception as e:
            print(f"[{datetime.now().strftime('%Y-%m-%d %H:%M:%S')}] 处理过程中发生错误：{str(e)}")
            self.status_var.set("错误")
            self.progress_info_var.set("处理失败")
        finally:
            # 恢复界面状态
            self.start_button.config(state=tk.NORMAL)
            self.stop_button.config(state=tk.DISABLED)
            
            # 停止日志重定向
            if self.log_redirector:
                self.log_redirector.stop()
            
            # 更新结果展示
            self.update_results()
    
    def process_single_order(self, order, store_list, logistics_list):
        """
        处理单个订单的完整逻辑
        """
        global_order_no = order.get("global_order_no")
        local_sku = order.get("local_sku")
        store_id = order.get("store_id")
        receiver_country_code = order.get("receiver_country_code")
        city = order.get("city")
        postal_code = order.get("postal_code")
        
        print(f"[{datetime.now().strftime('%Y-%m-%d %H:%M:%S')}] 处理订单 {global_order_no}")
        print(f"[{datetime.now().strftime('%Y-%m-%d %H:%M:%S')}] 订单信息：SKU={local_sku}, 店铺ID={store_id}")
        
        result = {
            "global_order_no": global_order_no,
            "status": "failed",
            "message": "",
            "final_fee": 0,
            "final_type_id": "",
            "final_wid": "",
            "final_channel_code": ""
        }

        # 步骤1：根据store_id匹配platform_name
        print(f"[{datetime.now().strftime('%Y-%m-%d %H:%M:%S')}] 步骤1：匹配店铺平台")
        store_item = next((s for s in store_list if s.get("store_id") == store_id), None)
        if not store_item:
            result["message"] = "未匹配到店铺信息"
            print(f"[{datetime.now().strftime('%Y-%m-%d %H:%M:%S')}] {result['message']}")
            return result
        platform_name = store_item.get("platform_name")
        print(f"[{datetime.now().strftime('%Y-%m-%d %H:%M:%S')}] 匹配到店铺：{platform_name}")

        # 步骤2：根据platform_name匹配物流渠道
        print(f"[{datetime.now().strftime('%Y-%m-%d %H:%M:%S')}] 步骤2：匹配物流渠道")
        matched_logistics = self.match_logistics_by_platform(platform_name, logistics_list)
        if not matched_logistics:
            result["message"] = "未匹配到可用物流渠道"
            print(f"[{datetime.now().strftime('%Y-%m-%d %H:%M:%S')}] {result['message']}")
            return result

        # 步骤3：根据sku获取有货的仓库ID
        print(f"[{datetime.now().strftime('%Y-%m-%d %H:%M:%S')}] 步骤3：查询库存信息")
        inventory_details = review_order.get_inventory_details(local_sku)
        valid_wids = [item.get("wid") for item in inventory_details if int(item.get("product_valid_num", 0)) > 0]
        print(f"[{datetime.now().strftime('%Y-%m-%d %H:%M:%S')}] 有货仓库：{valid_wids}")
        
        if not valid_wids:
            result["message"] = "无可用库存"
            print(f"[{datetime.now().strftime('%Y-%m-%d %H:%M:%S')}] {result['message']}")
            return result

        # 步骤4：根据有货仓库筛选物流渠道
        print(f"[{datetime.now().strftime('%Y-%m-%d %H:%M:%S')}] 步骤4：筛选物流渠道")
        filtered_logistics = self.filter_logistics_by_wid(matched_logistics, valid_wids)
        if not filtered_logistics:
            result["message"] = "无匹配有货仓库的物流渠道"
            print(f"[{datetime.now().strftime('%Y-%m-%d %H:%M:%S')}] {result['message']}")
            return result

        # 步骤5：计算中邮运费
        print(f"[{datetime.now().strftime('%Y-%m-%d %H:%M:%S')}] 步骤5：计算中邮运费")
        ems_ship_result = None
        ems_logistics = self.filter_logistics_by_provider(filtered_logistics, "中邮")
        if ems_logistics:
            ems_channel_codes = [log.get("channel_code") for log in ems_logistics if log.get("channel_code")]
            if ems_channel_codes:
                ems_spec = review_order.get_ems_product_spec(local_sku)
                if ems_spec and all([ems_spec.get("weight"), ems_spec.get("length"), ems_spec.get("width"), ems_spec.get("height")]):
                    try:
                        weight = float(ems_spec.get("weight"))
                        length = float(ems_spec.get("length"))
                        width = float(ems_spec.get("width"))
                        height = float(ems_spec.get("height"))
                        
                        print(f"[{datetime.now().strftime('%Y-%m-%d %H:%M:%S')}] 调用中邮运费试算接口...")
                        ems_fee_list = review_order.get_ems_ship_fee(
                            postcode=postal_code,
                            weight=weight,
                            warehouse="USEA,USWE",
                            channels=",".join(ems_channel_codes),
                            length=length,
                            width=width,
                            height=height
                        )
                        time.sleep(2)  # 避免请求过快
                        ems_ship_result = self.get_min_fee_shipment(ems_fee_list, ems_logistics)
                        time.sleep(10)  # 避免请求过快
                        if ems_ship_result:
                            print(f"[{datetime.now().strftime('%Y-%m-%d %H:%M:%S')}] 中邮最小运费：{ems_ship_result['totalFee']} {ems_ship_result['currency']}")
                    except Exception as e:
                        print(f"[{datetime.now().strftime('%Y-%m-%d %H:%M:%S')}] 计算中邮运费失败：{e}")

        # 步骤6：计算运德运费
        print(f"[{datetime.now().strftime('%Y-%m-%d %H:%M:%S')}] 步骤6：计算运德运费")
        wd_ship_result = None
        wd_logistics = self.filter_logistics_by_provider(filtered_logistics, "运德")
        if wd_logistics:
            wd_channel_codes = [log.get("channel_code") for log in wd_logistics if log.get("channel_code")]
            if wd_channel_codes:
                wd_spec = review_order.get_wd_product_spec(local_sku)
                if wd_spec and all([wd_spec.get("weight"), wd_spec.get("length"), wd_spec.get("width"), wd_spec.get("height")]):
                    try:
                        weight = float(wd_spec.get("weight"))
                        length = float(wd_spec.get("length"))
                        width = float(wd_spec.get("width"))
                        height = float(wd_spec.get("height"))
                        
                        print(f"[{datetime.now().strftime('%Y-%m-%d %H:%M:%S')}] 调用运德运费试算接口...")
                        wd_fee_list = review_order.get_wd_ship_fee(
                            channels=",".join(wd_channel_codes),
                            country=receiver_country_code,
                            city=city,
                            postcode=postal_code,
                            weight=weight,
                            length=length,
                            width=width,
                            height=height,
                            signatureService=0
                        )
                        time.sleep(2)  # 避免请求过快
                        wd_ship_result = self.get_min_fee_shipment(wd_fee_list, wd_logistics)
                        time.sleep(10)  # 避免请求过快
                        if wd_ship_result:
                            wd_fee_cny = wd_ship_result["totalFee"] * USD_TO_CNY_RATE
                            wd_ship_result["totalFee_cny"] = wd_fee_cny
                            print(f"[{datetime.now().strftime('%Y-%m-%d %H:%M:%S')}] 运德最小运费：{wd_ship_result['totalFee']} {wd_ship_result['currency']}（折合人民币{wd_fee_cny}元）")
                    except Exception as e:
                        print(f"[{datetime.now().strftime('%Y-%m-%d %H:%M:%S')}] 计算运德运费失败：{e}")

        # 步骤7：比较选择最优运费
        print(f"[{datetime.now().strftime('%Y-%m-%d %H:%M:%S')}] 步骤7：选择最优运费方案")
        final_choice = None
        compare_list = []
        
        if ems_ship_result and ems_ship_result["currency"] == "CNY":
            compare_list.append({
                "type": "ems",
                "fee_cny": ems_ship_result["totalFee"],
                "detail": ems_ship_result
            })
        if wd_ship_result and wd_ship_result["currency"] == "USD" and "totalFee_cny" in wd_ship_result:
            compare_list.append({
                "type": "wd",
                "fee_cny": wd_ship_result["totalFee_cny"],
                "detail": wd_ship_result
            })
        
        print(f"[{datetime.now().strftime('%Y-%m-%d %H:%M:%S')}] 可比较的运费方案：{compare_list}")
        if not compare_list:
            result["message"] = "无有效运费数据"
            print(f"[{datetime.now().strftime('%Y-%m-%d %H:%M:%S')}] {result['message']}")
            return result
        
        # 选择最小人民币运费
        min_compare = min(compare_list, key=lambda x: x["fee_cny"])
        final_choice = min_compare["detail"]
        print(f"[{datetime.now().strftime('%Y-%m-%d %H:%M:%S')}] 最优选择：{min_compare['type']} 仓库 | 运费 {min_compare['fee_cny']} 元")

        # 步骤8：修改订单
        print(f"[{datetime.now().strftime('%Y-%m-%d %H:%M:%S')}] 步骤8：执行订单修改")
        edit_result = review_order.edit_order(
            type_id=final_choice.get("type_id"),
            wid=final_choice.get("wid"),
            global_order_no=global_order_no
        )
        print(f"[{datetime.now().strftime('%Y-%m-%d %H:%M:%S')}] 订单修改结果：{edit_result}")
        
        if edit_result.get("code") == 0:
            result["status"] = "success"
            result["message"] = "订单修改成功"
            result["final_fee"] = min_compare["fee_cny"]
            result["final_type_id"] = final_choice.get("type_id")
            result["final_wid"] = final_choice.get("wid")
            result["final_channel_code"] = final_choice.get("channel_code")
            print(f"[{datetime.now().strftime('%Y-%m-%d %H:%M:%S')}] 订单处理成功")
        else:
            result["message"] = f"订单修改失败：{edit_result.get('message')}"
            print(f"[{datetime.now().strftime('%Y-%m-%d %H:%M:%S')}] {result['message']}")
        
        return result
    
    def match_logistics_by_platform(self, platform_name, logistics_list):
        """
        根据平台名称匹配对应的物流渠道
        """
        matched_logistics = []
        for logistics in logistics_list:
            logistics_provider = logistics.get("logistics_provider_name", "")
            if ("Amazon" in platform_name and "亚马逊" in logistics_provider) or \
               ("eBay" in platform_name and "eBay" in logistics_provider) or \
               ("Shopify" in platform_name and "独立站" in logistics_provider):
                matched_logistics.append(logistics)
        print(f"[{datetime.now().strftime('%Y-%m-%d %H:%M:%S')}] 平台{platform_name}匹配物流渠道数：{len(matched_logistics)}")
        return matched_logistics
    
    def filter_logistics_by_wid(self, logistics_list, valid_wids):
        """
        根据有货的仓库ID筛选物流渠道
        """
        if not valid_wids:
            return []
        filtered = [log for log in logistics_list if log.get("wid") in valid_wids]
        print(f"[{datetime.now().strftime('%Y-%m-%d %H:%M:%S')}] 筛选后物流渠道数：{len(filtered)}")
        return filtered
    
    def filter_logistics_by_provider(self, logistics_list, provider_keyword):
        """
        根据物流商名称关键词筛选（中邮/运德）
        """
        filtered = [log for log in logistics_list if provider_keyword in log.get("logistics_provider_name", "")]
        print(f"[{datetime.now().strftime('%Y-%m-%d %H:%M:%S')}] 按关键词{provider_keyword}筛选，结果数：{len(filtered)}")
        return filtered
    
    def get_min_fee_shipment(self, ship_fee_list, logistics_list):
        """
        从运费列表中找到最小运费对应的物流信息
        """
        if not ship_fee_list:
            return None
        
        # 过滤掉运费为空/0的情况
        valid_fee_list = [item for item in ship_fee_list if item.get("totalFee") and float(item.get("totalFee")) > 0]
        if not valid_fee_list:
            return None
        
        # 找到最小运费项
        min_fee_item = min(valid_fee_list, key=lambda x: float(x.get("totalFee")))
        min_channel_code = min_fee_item.get("channel_code")
        
        # 匹配对应的type_id和wid
        matched_log = next((log for log in logistics_list if log.get("channel_code") == min_channel_code), None)
        if not matched_log:
            return None
        
        result = {
            "totalFee": float(min_fee_item.get("totalFee")),
            "currency": min_fee_item.get("currency"),
            "type_id": matched_log.get("type_id"),
            "wid": matched_log.get("wid"),
            "channel_code": min_channel_code
        }
        return result
    
    def update_results(self):
        """
        更新结果展示
        """
        # 更新统计信息
        total_orders = len(self.process_results)
        success_count = sum(1 for res in self.process_results if res.get("status") == "success")
        failed_count = total_orders - success_count
        
        self.total_orders_var.set(f"总订单数: {total_orders}")
        self.success_orders_var.set(f"成功数: {success_count}")
        self.failed_orders_var.set(f"失败数: {failed_count}")
        
        # 更新结果表格
        for item in self.result_tree.get_children():
            self.result_tree.delete(item)
        
        for result in self.process_results:
            self.result_tree.insert("", tk.END, values=(
                result.get("global_order_no"),
                result.get("status"),
                result.get("final_fee", 0),
                result.get("final_type_id", ""),
                result.get("final_wid", ""),
                result.get("final_channel_code", ""),
                result.get("message", "")
            ))
    
    def monitor_process(self):
        """
        监控处理过程
        """
        while True:
            if not self.process_thread or not self.process_thread.is_alive():
                break
            time.sleep(0.5)

def main():
    """
    主函数
    """
    root = tk.Tk()
    app = BatchReviewGUI(root)
    root.mainloop()

if __name__ == "__main__":
    main()
