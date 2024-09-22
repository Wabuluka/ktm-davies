import { useEffect, useState } from 'react';
import { Book } from '../Types';

export type SelectableBook = Book & {
  selected: boolean;
};

export const useSelectableBooks = (books: Book[]) => {
  const [selectableBooks, setSelectableBooks] = useState<SelectableBook[]>(
    books.map((book) => ({ ...book, selected: false })),
  );

  useEffect(() => {
    setSelectableBooks(books.map((book) => ({ ...book, selected: false })));
  }, [books]);

  const onSelect = (event: React.ChangeEvent<HTMLInputElement>) => {
    setSelectableBooks((prevBooks) => {
      const id = event.target.parentElement?.dataset.id;
      const selected = event.target.checked;

      return prevBooks.map((book) =>
        String(book.id) === id ? { ...book, selected } : book,
      );
    });
  };

  const onSelectAll = (event: React.ChangeEvent<HTMLInputElement>) => {
    setSelectableBooks((prevBooks) =>
      prevBooks.map((book) => ({ ...book, selected: event.target.checked })),
    );
  };

  return {
    selectableBooks,
    onSelect,
    onSelectAll,
  };
};
