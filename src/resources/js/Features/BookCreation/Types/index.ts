export type Creation = {
  creation_type: string;
  displayed_type?: string;
  sort: number;
};

export type CreationFormData = {
  creator_id: string;
  creation_type: string;
  displayed_type?: string;
};

export type CreationOnBookForm = CreationFormData & {
  sort: number;
};
