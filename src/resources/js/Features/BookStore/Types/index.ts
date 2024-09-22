import { Store } from '@/Features/Store';

export type BookStore = {
  id: number;
  is_purchase_url_required: boolean;
  store: Store;
};
