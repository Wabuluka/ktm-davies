import { Site } from '@/Features/Site';

export type NewsCategory = {
  id: number;
  name: string;
  site: Site;
};

export type NewsCategoryFormData = {
  name: string;
};
