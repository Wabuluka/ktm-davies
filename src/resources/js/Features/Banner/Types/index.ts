import { Media } from '@/Features/Media';
import { Site } from '@/Features/Site/Types';

export type Banner = {
  id: number;
  name: string;
  url: string;
  new_tab: boolean;
  sort: number;
  displayed: boolean;
  placement_id: number;
  image: Media;
};

export type BannerPlacement = {
  id: number;
  name: string;
  max_banner_count: number | null;
  site: Site;
};

export type BannerFormData = {
  name: string;
  url: string;
  new_tab: boolean;
  displayed: boolean;
  image:
    | {
        operation: 'stay';
        file?: null;
      }
    | {
        operation: 'set';
        file: File;
      };
};
