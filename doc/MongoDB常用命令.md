# 进入MongoDB：
mongo 

# 显示所有数据库：
show dbs

# 使用/创建数据库：
use DATABASE

# 显示当前数据库集合列表：
show collections

# 列出当前集合中的文档：
db.COLLECTION.find().pretty()

# 删除当前数据库（注意一定要先选择当前数据库）：
db.dropDatabase()

# 删除集合：
db.COLLECTION.drop()

# 删除数据：
db.COLLECTION.remove({"KEY1": CONTENT1, "KEY2": CONTENT2, ...})

# 添加数据：
db.COLLECTION.insert({KEY1: CONTENT1, KEY2: CONTENT2, ...})
	
# 更新数据：
db.COLLECTION.update({'KEY1': CONTENT1}, {$set: {'KEY2': CONTENT2, 'KEY3': CONTENT3}, $unset: {'KEY4': true}})
	
# 修改Collection名称：
db.COLLECTION.renameCollection('NEW_NAME')
	
# 统计数据条数
db.COLLECTION.count()
	
# 获取数据大小
## 数据库信息
db.stats()
## 集合数据大小
db.COLLECTION.dataSize()
## 为集合分配的空间大小，包括未使用的空间
db.COLLECTION.storageSize()
## 集合中索引数据大小
db.COLLECTION.totalIndexSize()
## 集合中索引+data所占空间
db.COLLECTION.totalSize()
	
# 备份数据库
db.copyDatabase('fromdb', 'todb');