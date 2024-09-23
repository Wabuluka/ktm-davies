import { LabelType } from '@/Features/LabelType';

export type Label = {
  id: number;
  name: string;
  url: string;
  genre_id: number;
  sort: number;
  types: LabelType[] | [];
};


