-- 更新aigc_tasks表的task_type字段，添加image_to_image和text_to_image类型
ALTER TABLE aigc_tasks MODIFY COLUMN task_type ENUM('remove_defect', 'crop_png', 'crop_white_bg', 'resize', 'watermark', 'face_swap', 'multi_angle', 'image_to_image', 'text_to_image', 'other') NOT NULL COMMENT '任务类型';
