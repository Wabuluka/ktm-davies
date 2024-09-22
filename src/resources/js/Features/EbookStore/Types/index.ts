import { Store } from '@/Features/Store';

export type EbookStore = {
  id: number;
  is_purchase_url_required: boolean;
  store: Store;
};
