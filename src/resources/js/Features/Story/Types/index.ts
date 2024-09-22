import { Creator } from '@/Features/Creator';
import { Media } from '@/Features/Media';

export type Story = {
  id: number;
  title: string;
  trial_url?: string;
  creators: (Creator & {
    pivot: {
      sort: number;
    };
  })[];
  thumbnail?: Media;
};

export type StoryFormData = {
  title: string;
  trial_url?: string;
  creators: {
    id: string;
    sort: number;
  }[];
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
