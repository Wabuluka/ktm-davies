import { Series } from '@/Features/Series/Types';
import { Media } from '@/Features/Media';

export type Character = {
  id: number;
  name: string;
  description: string;
  thumbnail?: Media;
  series?: Series;
};

export type CharacterFormData = {
  name: string;
  description: string;
  series_id: Series['id'] | undefined;
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
