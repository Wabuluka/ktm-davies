import { Book, BookFormData } from '../../Types';

type Return = Pick<
  BookFormData,
  'caption' | 'description' | 'keywords' | 'trial_url' | 'survey_url'
>;

export function createDetailFieldInitialValues(
  book: Book | null = null,
): Return {
  return {
    caption: book?.caption || '',
    description: book?.description || '',
    keywords: book?.keywords || '',
    trial_url: book?.trial_url || '',
    survey_url: book?.survey_url || '',
  };
}
