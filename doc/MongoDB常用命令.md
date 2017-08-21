# ����MongoDB��
mongo 

# ��ʾ�������ݿ⣺
show dbs

# ʹ��/�������ݿ⣺
use DATABASE

# ��ʾ��ǰ���ݿ⼯���б�
show collections

# �г���ǰ�����е��ĵ���
db.COLLECTION.find().pretty()

# ɾ����ǰ���ݿ⣨ע��һ��Ҫ��ѡ��ǰ���ݿ⣩��
db.dropDatabase()

# ɾ�����ϣ�
db.COLLECTION.drop()

# ɾ�����ݣ�
db.COLLECTION.remove({"KEY1": CONTENT1, "KEY2": CONTENT2, ...})

# ������ݣ�
db.COLLECTION.insert({KEY1: CONTENT1, KEY2: CONTENT2, ...})
	
# �������ݣ�
db.COLLECTION.update({'KEY1': CONTENT1}, {$set: {'KEY2': CONTENT2, 'KEY3': CONTENT3}, $unset: {'KEY4': true}})
	
# �޸�Collection���ƣ�
db.COLLECTION.renameCollection('NEW_NAME')
	
# ͳ����������
db.COLLECTION.count()
	
# ��ȡ���ݴ�С
## ���ݿ���Ϣ
db.stats()
## �������ݴ�С
db.COLLECTION.dataSize()
## Ϊ���Ϸ���Ŀռ��С������δʹ�õĿռ�
db.COLLECTION.storageSize()
## �������������ݴ�С
db.COLLECTION.totalIndexSize()
## ����������+data��ռ�ռ�
db.COLLECTION.totalSize()
	
# �������ݿ�
db.copyDatabase('fromdb', 'todb');