import { Media } from '@/Features/Media';

export type ExternalLink = {
  id: number;
  title: string;
  url: string;
  thumbnail?: Media;
};

export type ExternalLinkFormData = {
  title: string;
  url: string;
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
