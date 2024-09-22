import { PropsWithChildren, createContext, useContext, useState } from 'react';
import { Book } from '../Types';

type Value = Book | null;

export const EditingBookContext = createContext<Value>(null);
export const SetEditingBookContext = createContext<React.Dispatch<
  React.SetStateAction<Value>
> | null>(null);

export function useEditingBook() {
  return useContext(EditingBookContext);
}
export function useSetEditingBook() {
  return useContext(SetEditingBookContext);
}

export function EditingBookProvider({
  book,
  children,
}: PropsWithChildren<{ book: Value }>) {
  const [editingBook, setEditingBook] = useState<Value>(book);

  return (
    <EditingBookContext.Provider value={editingBook}>
      <SetEditingBookContext.Provider value={setEditingBook}>
        {children}
      </SetEditingBookContext.Provider>
    </EditingBookContext.Provider>
  );
}
