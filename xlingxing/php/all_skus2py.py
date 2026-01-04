# ===================== 配置区（修改这3行即可） =====================
txt_file_path = "all_skus.txt"   # 要读取的原文本文件路径
py_save_path = "all_skus_data.py"        # 要另存的py文件名称/路径
list_var_name = "all_skus_list"         # 保存到py文件中的【数组(列表)变量名】，自定义即可
# ==================================================================

# 1. 读取文本文件，按行转干净的列表（去换行符，无空行干扰）
data_list = []
with open(txt_file_path, "r", encoding="utf-8") as f:
    for line in f:
        line_content = line.strip()  # 去掉换行符+每行首尾的空格/制表符，纯文本内容
        if line_content:  # 过滤掉文本中的空行
            data_list.append(line_content)

# 2. 将列表写入到 .py 文件中（核心步骤）
with open(py_save_path, "w", encoding="utf-8") as f:
    # 写入【变量名 = 列表】的标准python语法，直接可用
    f.write(f"{list_var_name} = {data_list}")

print(f"✅ 转换完成！已将文本按行转列表并保存至 {py_save_path}")
print(f"✅ 列表变量名：{list_var_name}，共 {len(data_list)} 行有效内容")
