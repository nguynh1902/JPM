import { openDB } from 'idb';
import axiosClient from '../api/axiosClient';

const DB_NAME = 'jpm-offline-db';
const DB_VERSION = 1;
const STORE_NAME = 'sync-queue';

export const initDB = async () => {
  return openDB(DB_NAME, DB_VERSION, {
    upgrade(db) {
      if (!db.objectStoreNames.contains(STORE_NAME)) {
        // Khởi tạo bảng lưu trữ với id tự tăng
        const store = db.createObjectStore(STORE_NAME, { keyPath: 'id', autoIncrement: true });
        // Tạo index để có thể truy xuất nhanh theo loại hoặc trạng thái nếu cần
        store.createIndex('type', 'type', { unique: false });
        store.createIndex('createdAt', 'createdAt', { unique: false });
      }
    },
  });
};

export const saveDraft = async (type, payload, endpoint, method = 'POST') => {
  const db = await initDB();
  const tx = db.transaction(STORE_NAME, 'readwrite');
  const store = tx.objectStore(STORE_NAME);
  
  const draftItem = {
    type,
    payload,
    endpoint,
    method,
    createdAt: new Date().toISOString(),
    status: 'pending' // pending | synced
  };

  await store.add(draftItem);
  await tx.done;
  
  return draftItem;
};

export const getPendingDrafts = async () => {
  const db = await initDB();
  return db.getAll(STORE_NAME);
};

export const removeDraft = async (id) => {
  const db = await initDB();
  const tx = db.transaction(STORE_NAME, 'readwrite');
  await tx.objectStore(STORE_NAME).delete(id);
  await tx.done;
};

// Hàm xử lý đồng bộ lên Server khi có mạng
export const syncOfflineData = async (toaster) => {
  if (!navigator.onLine) return;

  const db = await initDB();
  const drafts = await db.getAll(STORE_NAME);

  if (drafts.length === 0) return;

  if (toaster) {
    toaster.info(`Đang đồng bộ ${drafts.length} bản ghi bị gián đoạn...`);
  }

  for (const draft of drafts) {
    try {
      if (draft.method === 'POST') {
        await axiosClient.post(draft.endpoint, draft.payload);
      } else if (draft.method === 'PUT') {
        await axiosClient.put(draft.endpoint, draft.payload);
      } else if (draft.method === 'DELETE') {
        await axiosClient.delete(draft.endpoint);
      }
      
      // Xóa sau khi sync thành công
      await removeDraft(draft.id);
    } catch (error) {
      console.error('Lỗi khi đồng bộ draft:', draft.id, error);
    }
  }

  if (toaster) {
    toaster.success('Đồng bộ dữ liệu Offline thành công!');
  }
};

// Setup Listener tự động
export const setupOfflineListener = (toaster) => {
  window.addEventListener('online', () => {
    syncOfflineData(toaster);
  });
};
