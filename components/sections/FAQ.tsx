import type { FaqItem, HomeDictionary } from "@/content/types";

type FAQProps = {
  title: string;
  faqs: FaqItem[];
  dictionary?: HomeDictionary;
};

export function FAQ({ title, faqs, dictionary }: FAQProps) {
  return (
    <section className="bg-[#f6f8f9] py-20" id="faq">
      <div className="section-shell">
        <h2 className="text-3xl font-black text-neutral-950">{title}</h2>
        <div className="mt-8 divide-y divide-neutral-200 rounded-lg border border-neutral-200 bg-white">
          {faqs.map((faq, index) => (
            <details className="group p-6" key={faq.question} open={index === 0}>
              <summary className="cursor-pointer list-none text-lg font-black text-neutral-950">
                <span className="inline-flex w-full items-center justify-between gap-4">
                  {faq.question}
                  <span className="text-2xl text-teal-700 group-open:rotate-45">+</span>
                </span>
              </summary>
              {dictionary && index === 2 ? (
                <div className="mt-4 text-neutral-700">
                  <p>{dictionary.faq.a3}</p>
                  <ul className="mt-3 list-disc space-y-2 pl-5">
                    {[1, 2, 3, 4, 5].map((item) => (
                      <li key={item}>{dictionary.faq[`a3.i${item}`]}</li>
                    ))}
                  </ul>
                </div>
              ) : (
                <p className="mt-4 leading-7 text-neutral-700">{faq.answer}</p>
              )}
            </details>
          ))}
        </div>
      </div>
    </section>
  );
}
