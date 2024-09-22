import { Media } from '@/Features/Media';
import { Store } from '@/Features/Store';

export type Benefit = {
  id: number;
  name: string;
  paid: boolean;
  store: Store;
  thumbnail: Media | null;
};

export type BenefitFormData = {
  name: string;
  paid: boolean;
  storeId: Store['id'] | undefined;
  thumbnail:
    | {
        operation: 'stay' | 'delete';
        file?: null;
      }
    | {
        operation: 'set';
        file: File;
      };
};
