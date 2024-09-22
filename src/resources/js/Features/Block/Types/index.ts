export type BlockType = {
  id: number;
  name: string;
};

export type Block = {
  id: number;
  type_id: number;
  custom_title?: string;
  custom_content?: string;
  sort: number;
  displayed: boolean;
};

export type BlockOnBookForm = {
  id: string;
  type_id: string;
  custom_title?: string;
  custom_content?: string;
  sort: number;
  displayed: boolean;
};

export type BlockFormData = {
  custom_title?: string;
  custom_content?: string;
};

export type EbookStoreBlockFormData = Pick<BlockFormData, 'custom_content'>;
